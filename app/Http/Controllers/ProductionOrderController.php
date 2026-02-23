<?php

namespace App\Http\Controllers;

use App\Models\ProductionOrder;
use App\Models\ProductionOrderOutput;
use App\Models\Material;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductionOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductionOrder::with(['sourceMaterial', 'outputs.outputMaterial', 'user']);
        
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('po_number', 'like', "%{$search}%")
                  ->orWhereHas('sourceMaterial', function($mq) use ($search) {
                      $mq->where('material_name', 'like', "%{$search}%")
                         ->orWhere('material_code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('outputs.outputMaterial', function($oq) use ($search) {
                      $oq->where('material_name', 'like', "%{$search}%")
                         ->orWhere('material_code', 'like', "%{$search}%");
                  });
            });
        }
        
        $orders = $query->latest()->paginate(20)->withQueryString();
        
        return view('production-orders.index', compact('orders'));
    }

    public function create()
    {
        // Only materials that have outputs can be used for production
        $sourceMaterials = Material::whereHas('outputs')
            ->where('is_active', 1)
            ->orderBy('material_name')
            ->get();
        
        return view('production-orders.create', compact('sourceMaterials'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'source_material_id' => 'required|exists:materials,id',
            'source_quantity' => 'required|numeric|min:0.01',
            'production_line' => 'nullable|string',
            'planned_start_date' => 'required|date',
            'notes' => 'nullable|string',
            'outputs' => 'required|array|min:1',
            'outputs.*.output_material_id' => 'required|exists:materials,id',
            'outputs.*.expected_quantity' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();
        try {
            // Generate PO Number
            $lastPO = ProductionOrder::latest()->first();
            $poNumber = 'PO-' . date('Ymd') . '-' . str_pad(($lastPO ? $lastPO->id + 1 : 1), 4, '0', STR_PAD_LEFT);

            // Create Production Order
            $po = ProductionOrder::create([
                'po_number' => $poNumber,
                'source_material_id' => $validated['source_material_id'],
                'source_quantity' => $validated['source_quantity'],
                'production_line' => $validated['production_line'],
                'planned_start_date' => $validated['planned_start_date'],
                'notes' => $validated['notes'],
                'status' => 'DRAFT',
                'user_id' => Auth::id(),
            ]);

            // Create Production Order Outputs
            foreach ($validated['outputs'] as $output) {
                ProductionOrderOutput::create([
                    'production_order_id' => $po->id,
                    'output_material_id' => $output['output_material_id'],
                    'expected_quantity' => $output['expected_quantity'],
                ]);
            }

            DB::commit();

            return redirect()->route('production-orders.show', $po)
                ->with('success', 'Production Order berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat Production Order: ' . $e->getMessage())->withInput();
        }
    }

    public function show(ProductionOrder $productionOrder)
    {
        $productionOrder->load(['sourceMaterial', 'outputs.outputMaterial', 'user']);
        
        // Calculate material usage if completed
        $materialUsage = null;
        if ($productionOrder->status === 'COMPLETED') {
            $totalExpected = $productionOrder->outputs->sum('expected_quantity');
            $totalActual = $productionOrder->outputs->sum('actual_quantity');
            
            if ($totalExpected > 0) {
                $yieldRate = $totalActual / $totalExpected;
                $actualUsed = $productionOrder->source_quantity * $yieldRate;
                $returned = $productionOrder->source_quantity - $actualUsed;
                
                $materialUsage = [
                    'planned' => $productionOrder->source_quantity,
                    'actual_used' => $actualUsed,
                    'returned' => $returned,
                    'yield_rate' => $yieldRate * 100,
                ];
            }
        }
        
        return view('production-orders.show', compact('productionOrder', 'materialUsage'));
    }

    public function start(ProductionOrder $productionOrder)
    {
        if (!$productionOrder->canStart()) {
            return back()->with('error', 'Production Order tidak dapat dimulai');
        }

        DB::beginTransaction();
        try {
            // Check if source material stock is sufficient
            $sourceMaterial = $productionOrder->sourceMaterial;
            if ($sourceMaterial->current_stock < $productionOrder->source_quantity) {
                return back()->with('error', 'Stock material tidak mencukupi. Stock saat ini: ' . number_format($sourceMaterial->current_stock, 2) . ' ' . $sourceMaterial->unit_of_measure);
            }

            // Create Stock OUT transaction for source material
            $transactionNumber = 'TRX-' . date('YmdHis') . '-' . rand(1000, 9999);
            
            StockTransaction::create([
                'transaction_number' => $transactionNumber,
                'transaction_date' => now(),
                'transaction_type' => 'OUT',
                'material_id' => $productionOrder->source_material_id,
                'quantity' => $productionOrder->source_quantity,
                'reference_number' => $productionOrder->po_number,
                'notes' => 'Material untuk produksi - ' . $productionOrder->po_number,
                'user_id' => Auth::id(),
            ]);

            // Stock automatically updated by StockTransaction observer

            // Update production order status
            $productionOrder->status = 'PROCESSING';
            $productionOrder->actual_start_date = now();
            $productionOrder->save();

            DB::commit();

            return back()->with('success', 'Production Order dimulai. Material telah dikurangi dari stock.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memulai production: ' . $e->getMessage());
        }
    }

    public function complete(Request $request, ProductionOrder $productionOrder)
    {
        if (!$productionOrder->canComplete()) {
            return back()->with('error', 'Production Order tidak dapat diselesaikan');
        }

        $validated = $request->validate([
            'outputs' => 'required|array',
            'outputs.*.id' => 'required|exists:production_order_outputs,id',
            'outputs.*.actual_quantity' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $totalExpected = 0;
            $totalActual = 0;

            // Update actual quantities and create stock IN transactions
            foreach ($validated['outputs'] as $outputData) {
                $output = ProductionOrderOutput::find($outputData['id']);
                $output->actual_quantity = $outputData['actual_quantity'];
                $output->save();

                $totalExpected += $output->expected_quantity;
                $totalActual += $output->actual_quantity;

                // Create Stock IN transaction for output material
                if ($output->actual_quantity > 0) {
                    $transactionNumber = 'TRX-' . date('YmdHis') . '-' . rand(1000, 9999);
                    
                    // Get output material and determine warehouse based on category
                    $outputMaterial = \App\Models\Material::find($output->output_material_id);
                    $warehouse = \App\Models\Warehouse::getForMaterialCategory($outputMaterial->category ?? 'General');
                    
                    StockTransaction::create([
                        'transaction_number' => $transactionNumber,
                        'transaction_date' => now(),
                        'transaction_type' => 'IN',
                        'material_id' => $output->output_material_id,
                        'warehouse_id' => $warehouse?->id,
                        'quantity' => $output->actual_quantity,
                        'reference_number' => $productionOrder->po_number,
                        'notes' => 'Hasil produksi - ' . $productionOrder->po_number . ($warehouse ? ' (Gudang: ' . $warehouse->warehouse_name . ')' : ''),
                        'user_id' => Auth::id(),
                    ]);

                    // Stock automatically updated by StockTransaction observer
                }
            }

            // Calculate yield rate and return unused material
            if ($totalExpected > 0) {
                $yieldRate = $totalActual / $totalExpected; // e.g., 300/600 = 0.5 (50%)
                $actualMaterialUsed = $productionOrder->source_quantity * $yieldRate; // e.g., 150 * 0.5 = 75
                $unusedMaterial = $productionOrder->source_quantity - $actualMaterialUsed; // e.g., 150 - 75 = 75

                // If there's unused material (yield < 100%), return it to stock
                if ($unusedMaterial > 0.01) { // threshold 0.01 to avoid floating point issues
                    $transactionNumber = 'TRX-' . date('YmdHis') . '-' . rand(1000, 9999);
                    
                    // Return to warehouse based on source material category
                    $sourceMaterial = \App\Models\Material::find($productionOrder->source_material_id);
                    $warehouse = \App\Models\Warehouse::getForMaterialCategory($sourceMaterial->category ?? 'Raw Material');
                    
                    StockTransaction::create([
                        'transaction_number' => $transactionNumber,
                        'transaction_date' => now(),
                        'transaction_type' => 'ADJUSTMENT',
                        'material_id' => $productionOrder->source_material_id,
                        'warehouse_id' => $warehouse?->id,
                        'quantity' => $unusedMaterial,
                        'reference_number' => $productionOrder->po_number . '-RETURN',
                        'notes' => sprintf(
                            'Return unused material (Yield: %.1f%%, Used: %.2f, Unused: %.2f) - %s%s',
                            $yieldRate * 100,
                            $actualMaterialUsed,
                            $unusedMaterial,
                            $productionOrder->po_number,
                            $warehouse ? ' (Gudang: ' . $warehouse->warehouse_name . ')' : ''
                        ),
                        'user_id' => Auth::id(),
                    ]);

                    // Stock automatically updated by StockTransaction observer
                }
            }

            // Update production order status
            $productionOrder->status = 'COMPLETED';
            $productionOrder->actual_complete_date = now();
            $productionOrder->save();

            DB::commit();

            $message = 'Production Order selesai. Hasil produksi telah ditambahkan ke stock.';
            if (isset($unusedMaterial) && $unusedMaterial > 0.01) {
                $message .= sprintf(' Material yang tidak terpakai (%.2f %s) telah dikembalikan ke stock.', 
                    $unusedMaterial, 
                    $productionOrder->sourceMaterial->unit_of_measure
                );
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyelesaikan production: ' . $e->getMessage());
        }
    }

    public function cancel(ProductionOrder $productionOrder)
    {
        if (!$productionOrder->canCancel()) {
            return back()->with('error', 'Production Order tidak dapat dibatalkan');
        }

        DB::beginTransaction();
        try {
            // If status is PROCESSING, need to return the material stock
            if ($productionOrder->status === 'PROCESSING') {
                // Create adjustment transaction to return material
                $transactionNumber = 'TRX-' . date('YmdHis') . '-' . rand(1000, 9999);
                
                // Return to warehouse based on source material category
                $sourceMaterial = $productionOrder->sourceMaterial;
                $warehouse = \App\Models\Warehouse::getForMaterialCategory($sourceMaterial->category ?? 'Raw Material');
                
                StockTransaction::create([
                    'transaction_number' => $transactionNumber,
                    'transaction_date' => now(),
                    'transaction_type' => 'ADJUSTMENT',
                    'material_id' => $productionOrder->source_material_id,
                    'warehouse_id' => $warehouse?->id,
                    'quantity' => $productionOrder->source_quantity,
                    'reference_number' => $productionOrder->po_number . '-CANCELLED',
                    'notes' => 'Return material dari production order yang dibatalkan - ' . $productionOrder->po_number . ($warehouse ? ' (Gudang: ' . $warehouse->warehouse_name . ')' : ''),
                    'user_id' => Auth::id(),
                ]);

                // Update material stock
                $sourceMaterial->current_stock += $productionOrder->source_quantity;
                $sourceMaterial->save();
            }

            // Update production order status
            $productionOrder->status = 'CANCELLED';
            $productionOrder->save();

            DB::commit();

            return back()->with('success', 'Production Order dibatalkan' . ($productionOrder->status === 'PROCESSING' ? ' dan material dikembalikan ke stock.' : '.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan production: ' . $e->getMessage());
        }
    }

    public function getOutputs(Request $request)
    {
        $materialId = $request->input('material_id');
        $quantity = $request->input('quantity', 1);

        $material = Material::with('outputs.outputMaterial')->find($materialId);
        
        if (!$material) {
            return response()->json(['error' => 'Material not found'], 404);
        }

        $outputs = $material->outputs->where('is_active', true)->map(function($output) use ($quantity) {
            return [
                'id' => $output->id,
                'output_material_id' => $output->output_material_id,
                'output_material_code' => $output->outputMaterial->material_code,
                'output_material_name' => $output->outputMaterial->material_name,
                'unit_of_measure' => $output->outputMaterial->unit_of_measure,
                'quantity_per_unit' => $output->quantity_per_unit,
                'expected_quantity' => $output->quantity_per_unit * $quantity,
            ];
        });

        return response()->json([
            'material' => [
                'code' => $material->material_code,
                'name' => $material->material_name,
                'unit' => $material->unit_of_measure,
                'current_stock' => $material->current_stock,
            ],
            'outputs' => $outputs
        ]);
    }
}

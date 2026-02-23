<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Helpers\ExcelHelper;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        $query = Warehouse::query();
        
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('warehouse_code', 'like', "%{$search}%")
                  ->orWhere('warehouse_name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }
        
        $warehouses = $query->latest()->paginate(20)->withQueryString();
        return view('warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        return view('warehouses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_code' => 'required|unique:warehouses',
            'warehouse_name' => 'required',
            'description' => 'nullable',
            'location' => 'nullable',
        ]);

        Warehouse::create($validated);

        return redirect()->route('warehouses.index')
            ->with('success', 'Warehouse berhasil ditambahkan');
    }

    public function edit(Warehouse $warehouse)
    {
        return view('warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'warehouse_code' => 'required|unique:warehouses,warehouse_code,' . $warehouse->id,
            'warehouse_name' => 'required',
            'description' => 'nullable',
            'location' => 'nullable',
            'is_active' => 'boolean',
        ]);

        $warehouse->update($validated);

        return redirect()->route('warehouses.index')
            ->with('success', 'Warehouse berhasil diupdate');
    }

    public function destroy(Warehouse $warehouse)
    {
        try {
            $warehouse->delete();
            return redirect()->route('warehouses.index')
                ->with('success', 'Warehouse berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('warehouses.index')
                ->with('error', 'Warehouse tidak bisa dihapus karena memiliki transaksi');
        }
    }

    public function export()
    {
        $warehouses = Warehouse::all();
        
        $data = $warehouses->map(function($warehouse) {
            return [
                $warehouse->warehouse_code,
                $warehouse->warehouse_name,
                $warehouse->location,
                $warehouse->capacity,
                $warehouse->is_active ? 'Active' : 'Inactive',
            ];
        })->toArray();
        
        $headers = [
            'Warehouse Code',
            'Warehouse Name',
            'Location',
            'Capacity',
            'Status',
        ];
        
        return ExcelHelper::export($data, $headers, 'warehouses_' . date('YmdHis') . '.xlsx');
    }

    public function downloadTemplate()
    {
        $headers = [
            'warehouse_code',
            'warehouse_name',
            'location',
            'description',
            'status'
        ];
        
        $columnWidths = [15, 30, 40, 40, 12];
        
        $sampleData = [
            [
                'WH-001',
                'Gudang Utama',
                'Jl. Industri No. 10, Jakarta',
                'Gudang utama untuk raw material',
                'Active'
            ],
            [
                'WH-002',
                'Gudang Produksi',
                'Jl. Raya Pabrik No. 5, Bekasi',
                'Gudang khusus work in process',
                'Active'
            ],
            [
                'WH-003',
                'Gudang Finished Goods',
                'Jl. Logistik No. 20, Tangerang',
                'Gudang penyimpanan produk jadi',
                'Active'
            ],
        ];
        
        return ExcelHelper::createTemplate($headers, 'warehouses_import_template.xlsx', $columnWidths, $sampleData);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            $data = ExcelHelper::import($request->file('file'), 1);
            
            $errors = [];
            $successCount = 0;
            
            foreach ($data as $index => $row) {
                $rowNumber = $index + 2;
                
                // Skip empty rows
                if (empty($row['warehouse_code']) && empty($row['warehouse_name'])) {
                    continue;
                }
                
                // Validate row
                $validator = Validator::make($row, [
                    'warehouse_code' => 'required|unique:warehouses,warehouse_code',
                    'warehouse_name' => 'required',
                ], [
                    'warehouse_code.required' => 'Kode warehouse wajib diisi',
                    'warehouse_code.unique' => 'Kode warehouse sudah ada',
                    'warehouse_name.required' => 'Nama warehouse wajib diisi',
                ]);
                
                if ($validator->fails()) {
                    $errors[] = "Baris {$rowNumber}: " . implode(', ', $validator->errors()->all());
                    continue;
                }
                
                // Create warehouse
                Warehouse::create([
                    'warehouse_code' => trim($row['warehouse_code']),
                    'warehouse_name' => trim($row['warehouse_name']),
                    'location' => isset($row['location']) ? trim($row['location']) : null,
                    'description' => isset($row['description']) ? trim($row['description']) : null,
                    'is_active' => !isset($row['status']) || strtolower(trim($row['status'])) === 'active',
                ]);
                
                $successCount++;
            }
            
            if (count($errors) > 0) {
                return redirect()->route('warehouses.index')
                    ->with('error', 'Import selesai dengan error: ' . implode(' | ', array_slice($errors, 0, 5)));
            }
            
            return redirect()->route('warehouses.index')
                ->with('success', "{$successCount} warehouses berhasil diimport");
        } catch (\Exception $e) {
            return redirect()->route('warehouses.index')
                ->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    public function exportPdf()
    {
        $warehouses = Warehouse::all();
        $pdf = Pdf::loadView('pdf.warehouses', compact('warehouses'));
        return $pdf->download('warehouses_' . date('YmdHis') . '.pdf');
    }
}

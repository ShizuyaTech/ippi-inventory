<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\StockTransaction;
use App\Models\Warehouse;
use App\Models\Supplier;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = StockTransaction::with(['material', 'warehouse', 'supplier', 'customer', 'user']);
        
        // Filter based on user role
        $user = Auth::user();
        if ($user->isSupplier() && $user->supplier_id) {
            // Supplier can only see their own transactions
            $query->where('supplier_id', $user->supplier_id);
        }
        // Admin and Staff can see all transactions (no additional filter needed)
        
        if ($request->filled('type')) {
            $query->where('transaction_type', $request->type);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }
        
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('material', function($mq) use ($search) {
                      $mq->where('material_name', 'like', "%{$search}%")
                         ->orWhere('material_code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('supplier', function($sq) use ($search) {
                      $sq->where('supplier_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('customer_name', 'like', "%{$search}%");
                  });
            });
        }
        
        $transactions = $query->latest()->paginate(20)->withQueryString();
        
        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $user = Auth::user();
        
        $materials = Material::where('is_active', true)->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        $customers = Customer::where('is_active', true)->get();
        
        // Filter suppliers based on user role
        if ($user->isSupplier() && $user->supplier_id) {
            // Supplier can only select their own company
            $suppliers = Supplier::where('id', $user->supplier_id)->where('is_active', true)->get();
        } else {
            // Admin and Staff can see all suppliers
            $suppliers = Supplier::where('is_active', true)->get();
        }
        
        return view('transactions.create', compact('materials', 'warehouses', 'suppliers', 'customers'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'transaction_type' => 'required|in:IN,OUT,ADJUSTMENT',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'customer_id' => 'nullable|exists:customers,id',
            'reference_number' => 'nullable',
            'notes' => 'nullable',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.notes' => 'nullable',
        ]);

        // For supplier users, force their supplier_id
        if ($user->isSupplier() && $user->supplier_id) {
            $validated['supplier_id'] = $user->supplier_id;
        }

        DB::beginTransaction();
        try {
            // Create transaction for each item
            foreach ($validated['items'] as $item) {
                // Generate transaction number
                $lastTransaction = StockTransaction::whereDate('created_at', today())->latest()->first();
                $counter = $lastTransaction ? intval(substr($lastTransaction->transaction_number, -4)) + 1 : 1;
                $transactionNumber = 'TRX-' . date('Ymd') . '-' . str_pad($counter, 4, '0', STR_PAD_LEFT);
                
                StockTransaction::create([
                    'transaction_number' => $transactionNumber,
                    'transaction_date' => $validated['transaction_date'],
                    'transaction_type' => $validated['transaction_type'],
                    'material_id' => $item['material_id'],
                    'warehouse_id' => $validated['warehouse_id'] ?? null,
                    'supplier_id' => $validated['supplier_id'] ?? null,
                    'customer_id' => $validated['customer_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'reference_number' => $validated['reference_number'] ?? null,
                    'notes' => $item['notes'] ?? $validated['notes'] ?? null,
                    'user_id' => $user->id,
                ]);
            }

            DB::commit();
            
            return redirect()->route('transactions.index')
                ->with('success', count($validated['items']) . ' transaksi berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage());
        }
    }

    public function show(StockTransaction $transaction)
    {
        $user = Auth::user();
        
        // Check access permission for supplier users
        if ($user->isSupplier() && $user->supplier_id) {
            if ($transaction->supplier_id !== $user->supplier_id) {
                abort(403, 'Unauthorized access to this transaction.');
            }
        }
        
        $transaction->load(['material', 'warehouse', 'supplier', 'customer', 'user']);
        return view('transactions.show', compact('transaction'));
    }
}

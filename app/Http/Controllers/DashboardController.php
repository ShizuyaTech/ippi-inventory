<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\ProductionOrder;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Calculate total material IN today
        $totalMaterialIn = StockTransaction::where('transaction_type', 'IN')
            ->whereDate('transaction_date', today())
            ->sum('quantity');
        
        // Calculate total material OUT today
        $totalMaterialOut = StockTransaction::where('transaction_type', 'OUT')
            ->whereDate('transaction_date', today())
            ->sum('quantity');
        
        // Count production orders
        $totalProductionOrders = ProductionOrder::count();
        
        $lowStockMaterials = Material::whereRaw('current_stock <= minimum_stock')->count();
        
        $recentTransactions = StockTransaction::with(['material', 'user'])
            ->latest()
            ->take(10)
            ->get();
        
        $lowStockItems = Material::whereRaw('current_stock <= minimum_stock')
            ->where('is_active', true)
            ->orderBy('current_stock', 'asc')
            ->take(10)
            ->get();
        
        // Stock IN grouped by supplier today
        $stockInBySupplier = StockTransaction::with('supplier')
            ->where('transaction_type', 'IN')
            ->whereDate('transaction_date', today())
            ->whereNotNull('supplier_id')
            ->select('supplier_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('supplier_id')
            ->get();
        
        // Stock OUT grouped by customer today
        $stockOutByCustomer = StockTransaction::with('customer')
            ->where('transaction_type', 'OUT')
            ->whereDate('transaction_date', today())
            ->whereNotNull('customer_id')
            ->select('customer_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('customer_id')
            ->get();
        
        return view('dashboard', compact(
            'totalMaterialIn',
            'totalMaterialOut',
            'totalProductionOrders',
            'lowStockMaterials',
            'recentTransactions',
            'lowStockItems',
            'stockInBySupplier',
            'stockOutByCustomer'
        ));
    }

    public function allStock(Request $request)
    {
        $query = Material::query();
        
        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('material_code', 'like', "%{$search}%")
                  ->orWhere('material_name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('unit_of_measure', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }
        
        // Filter by category
        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }
        
        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $statusValue = $request->status === 'active' ? 1 : 0;
            $query->where('is_active', $statusValue);
        }
        
        // Sort by column
        $sortBy = $request->get('sort_by', 'material_code');
        $sortOrder = $request->get('sort_order', 'asc');
        
        // Validate sort columns
        $allowedSortColumns = ['material_code', 'material_name', 'category', 'current_stock', 'minimum_stock', 'unit_of_measure'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'material_code';
        }
        
        $query->orderBy($sortBy, $sortOrder);
        
        // Get all unique categories for filter dropdown
        $categories = Material::distinct()->pluck('category')->filter()->sort()->values();
        
        $materials = $query->paginate(50)->withQueryString();
        
        // Calculate low stock statistics per category
        $lowStockRawMaterial = Material::where('category', 'Raw Material')
            ->whereRaw('current_stock <= minimum_stock')
            ->where('is_active', true)
            ->count();
        
        $lowStockWIP = Material::where('category', 'WIP')
            ->whereRaw('current_stock <= minimum_stock')
            ->where('is_active', true)
            ->count();
        
        $lowStockFinishedGoods = Material::where('category', 'Finished Goods')
            ->whereRaw('current_stock <= minimum_stock')
            ->where('is_active', true)
            ->count();
        
        $lowStockConsumables = Material::where('category', 'Consumables')
            ->whereRaw('current_stock <= minimum_stock')
            ->where('is_active', true)
            ->count();
        
        $lowStockTools = Material::where('category', 'Tools')
            ->whereRaw('current_stock <= minimum_stock')
            ->where('is_active', true)
            ->count();
        
        return view('all-stock', compact(
            'materials',
            'categories',
            'lowStockRawMaterial',
            'lowStockWIP',
            'lowStockFinishedGoods',
            'lowStockConsumables',
            'lowStockTools'
        ));
    }
}

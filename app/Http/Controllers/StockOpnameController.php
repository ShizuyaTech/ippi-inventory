<?php

namespace App\Http\Controllers;

use App\Models\StockOpname;
use App\Models\Material;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockOpnameController extends Controller
{
    public function index()
    {
        $opnames = StockOpname::with(['material', 'warehouse', 'user'])
            ->latest()
            ->paginate(20);
        
        return view('opname.index', compact('opnames'));
    }

    public function create()
    {
        $materials = Material::where('is_active', true)->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        
        return view('opname.create', compact('materials', 'warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'opname_date' => 'required|date',
            'material_id' => 'required|exists:materials,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'physical_stock' => 'required|numeric|min:0',
            'notes' => 'nullable',
        ]);

        // Get current stock as system stock
        $material = Material::find($validated['material_id']);
        $validated['system_stock'] = $material->current_stock;

        // Generate opname number
        $lastOpname = StockOpname::whereDate('created_at', today())->latest()->first();
        $counter = $lastOpname ? intval(substr($lastOpname->opname_number, -4)) + 1 : 1;
        $validated['opname_number'] = 'OPN-' . date('Ymd') . '-' . str_pad($counter, 4, '0', STR_PAD_LEFT);
        $validated['user_id'] = Auth::id();
        $validated['status'] = 'DRAFT';

        StockOpname::create($validated);

        return redirect()->route('opname.index')
            ->with('success', 'Stock Opname berhasil disimpan');
    }

    public function show(StockOpname $opname)
    {
        $opname->load(['material', 'warehouse', 'user']);
        return view('opname.show', compact('opname'));
    }

    public function approve(StockOpname $opname)
    {
        if ($opname->status !== 'DRAFT') {
            return redirect()->back()->with('error', 'Hanya status DRAFT yang bisa di-approve');
        }

        $opname->update(['status' => 'APPROVED']);

        return redirect()->back()->with('success', 'Stock Opname berhasil di-approve');
    }

    public function post(StockOpname $opname)
    {
        if ($opname->status !== 'APPROVED') {
            return redirect()->back()->with('error', 'Hanya status APPROVED yang bisa di-post');
        }

        $opname->update(['status' => 'POSTED']);

        return redirect()->back()->with('success', 'Stock Opname berhasil di-post dan adjustment dibuat');
    }
}

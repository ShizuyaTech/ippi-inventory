<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Exports\WarehousesExport;
use App\Exports\WarehousesTemplateExport;
use App\Imports\WarehousesImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

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
        return Excel::download(new WarehousesExport, 'warehouses_' . date('YmdHis') . '.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new WarehousesTemplateExport, 'warehouses_import_template.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new WarehousesImport, $request->file('file'));
            return redirect()->route('warehouses.index')
                ->with('success', 'Warehouses berhasil diimport');
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

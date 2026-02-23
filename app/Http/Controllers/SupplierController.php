<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Exports\SuppliersExport;
use App\Exports\SuppliersTemplateExport;
use App\Imports\SuppliersImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();
        
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('supplier_code', 'like', "%{$search}%")
                  ->orWhere('supplier_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $suppliers = $query->latest()->paginate(20)->withQueryString();
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_code' => 'required|unique:suppliers',
            'supplier_name' => 'required',
            'contact_person' => 'nullable',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'address' => 'nullable',
            'city' => 'nullable',
        ]);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil ditambahkan');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'supplier_code' => 'required|unique:suppliers,supplier_code,' . $supplier->id,
            'supplier_name' => 'required',
            'contact_person' => 'nullable',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'address' => 'nullable',
            'city' => 'nullable',
            'is_active' => 'boolean',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil diupdate');
    }

    public function destroy(Supplier $supplier)
    {
        try {
            $supplier->delete();
            return redirect()->route('suppliers.index')
                ->with('success', 'Supplier berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('suppliers.index')
                ->with('error', 'Supplier tidak bisa dihapus karena memiliki transaksi');
        }
    }

    public function export()
    {
        return Excel::download(new SuppliersExport, 'suppliers_' . date('YmdHis') . '.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new SuppliersTemplateExport, 'suppliers_import_template.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new SuppliersImport, $request->file('file'));
            return redirect()->route('suppliers.index')
                ->with('success', 'Suppliers berhasil diimport');
        } catch (\Exception $e) {
            return redirect()->route('suppliers.index')
                ->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    public function exportPdf()
    {
        $suppliers = Supplier::all();
        $pdf = Pdf::loadView('pdf.suppliers', compact('suppliers'));
        return $pdf->download('suppliers_' . date('YmdHis') . '.pdf');
    }
}

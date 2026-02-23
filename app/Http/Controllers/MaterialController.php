<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Exports\MaterialsExport;
use App\Exports\MaterialsTemplateExport;
use App\Imports\MaterialsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $query = Material::query();
        
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('material_code', 'like', "%{$search}%")
                  ->orWhere('material_name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $materials = $query->latest()->paginate(20)->withQueryString();
        return view('materials.index', compact('materials'));
    }

    public function create()
    {
        return view('materials.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'material_code' => 'required|unique:materials',
            'material_name' => 'required',
            'description' => 'nullable',
            'unit_of_measure' => 'required',
            'category' => 'nullable',
            'minimum_stock' => 'required|numeric|min:0',
            'location' => 'nullable',
        ]);

        $material = Material::create($validated);

        return redirect()->route('materials.index')
            ->with('success', 'Material berhasil ditambahkan');
    }

    public function show(Material $material)
    {
        $transactions = $material->transactions()->with(['warehouse', 'supplier', 'customer', 'user'])
            ->latest()
            ->paginate(20);
        
        $allMaterials = Material::where('is_active', 1)->orderBy('material_name')->get();
        
        return view('materials.show', compact('material', 'transactions', 'allMaterials'));
    }

    public function edit(Material $material)
    {
        $allMaterials = Material::where('is_active', 1)->orderBy('material_name')->get();
        return view('materials.edit', compact('material', 'allMaterials'));
    }

    public function update(Request $request, Material $material)
    {
        $validated = $request->validate([
            'material_code' => 'required|unique:materials,material_code,' . $material->id,
            'material_name' => 'required',
            'description' => 'nullable',
            'unit_of_measure' => 'required',
            'category' => 'nullable',
            'minimum_stock' => 'required|numeric|min:0',
            'location' => 'nullable',
        ]);
        
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $material->update($validated);

        return redirect()->route('materials.index')
            ->with('success', 'Material berhasil diupdate');
    }

    public function destroy(Material $material)
    {
        try {
            $material->delete();
            return redirect()->route('materials.index')
                ->with('success', 'Material berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('materials.index')
                ->with('error', 'Material tidak bisa dihapus karena memiliki transaksi');
        }
    }

    public function export()
    {
        return Excel::download(new MaterialsExport, 'materials_' . date('YmdHis') . '.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new MaterialsTemplateExport, 'materials_import_template.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            $import = new MaterialsImport;
            Excel::import($import, $request->file('file'));
            
            // Get failures if any
            $failures = $import->failures();
            
            if ($failures->count() > 0) {
                $errorMessages = [];
                foreach ($failures as $failure) {
                    $errorMessages[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
                }
                
                return redirect()->route('materials.index')
                    ->with('error', 'Import selesai dengan error: ' . implode(' | ', array_slice($errorMessages, 0, 5)));
            }
            
            return redirect()->route('materials.index')
                ->with('success', 'Materials berhasil diimport');
        } catch (\Exception $e) {
            return redirect()->route('materials.index')
                ->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    public function exportPdf()
    {
        $materials = Material::all();
        $pdf = Pdf::loadView('pdf.materials', compact('materials'));
        return $pdf->download('materials_' . date('YmdHis') . '.pdf');
    }
}

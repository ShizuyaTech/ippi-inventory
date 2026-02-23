<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Helpers\ExcelHelper;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;

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
        $materials = Material::all();
        
        $data = $materials->map(function($material) {
            return [
                $material->material_code,
                $material->material_name,
                $material->description,
                $material->category,
                $material->unit_of_measure,
                $material->current_stock,
                $material->is_active ? 'Active' : 'Inactive',
            ];
        })->toArray();
        
        $headers = [
            'Material Code',
            'Material Name',
            'Description',
            'Category',
            'Unit of Measure',
            'Current Stock',
            'Status',
        ];
        
        return ExcelHelper::export($data, $headers, 'materials_' . date('YmdHis') . '.xlsx');
    }

    public function downloadTemplate()
    {
        $headers = [
            'material_code',
            'material_name',
            'description',
            'category',
            'unit_of_measure',
            'current_stock',
            'minimum_stock',
            'status'
        ];
        
        $columnWidths = [15, 30, 35, 20, 18, 15, 15, 12];
        
        $sampleData = [
            [
                'RM-001',
                'Steel Sheet Cold Rolled 1.2mm',
                'Raw material for stamping',
                'RAW MATERIAL',
                'SHEET',
                100,
                10,
                'Active'
            ],
            [
                'FG-001',
                'Bracket Type A',
                'Finished product',
                'FINISHED GOODS',
                'PCS',
                50,
                20,
                'Active'
            ],
            [
                'WIP-001',
                'Semi-finished Bracket',
                'Work in progress',
                'SEMI-FINISHED',
                'PCS',
                0,
                0,
                'Active'
            ],
        ];
        
        return ExcelHelper::createTemplate($headers, 'materials_import_template.xlsx', $columnWidths, $sampleData);
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
                $rowNumber = $index + 2; // +2 because index starts at 0 and header is row 1
                
                // Skip empty rows
                if (empty($row['material_code']) && empty($row['material_name']) && empty($row['unit_of_measure'])) {
                    continue;
                }
                
                // Skip instruction/note rows
                $firstCol = trim($row['material_code'] ?? '');
                if (preg_match('/^(INSTRUCTIONS|CATATAN|NOTE|PANDUAN|PETUNJUK|\d+\.)/i', $firstCol)) {
                    continue;
                }
                
                // Validate row
                $validator = Validator::make($row, [
                    'material_code' => 'required|unique:materials,material_code',
                    'material_name' => 'required',
                    'unit_of_measure' => 'required',
                ], [
                    'material_code.required' => 'Kode material wajib diisi',
                    'material_code.unique' => 'Kode material sudah ada di database',
                    'material_name.required' => 'Nama material wajib diisi',
                    'unit_of_measure.required' => 'Unit of measure wajib diisi',
                ]);
                
                if ($validator->fails()) {
                    $errors[] = "Baris {$rowNumber}: " . implode(', ', $validator->errors()->all());
                    continue;
                }
                
                // Create material
                Material::create([
                    'material_code' => trim($row['material_code']),
                    'material_name' => trim($row['material_name']),
                    'description' => isset($row['description']) ? trim($row['description']) : null,
                    'category' => isset($row['category']) ? trim($row['category']) : null,
                    'unit_of_measure' => isset($row['unit_of_measure']) ? strtoupper(trim($row['unit_of_measure'])) : null,
                    'current_stock' => isset($row['current_stock']) ? floatval($row['current_stock']) : 0,
                    'minimum_stock' => isset($row['minimum_stock']) ? floatval($row['minimum_stock']) : 0,
                    'location' => null,
                    'is_active' => !isset($row['status']) || strtolower(trim($row['status'])) === 'active',
                ]);
                
                $successCount++;
            }
            
            if (count($errors) > 0) {
                return redirect()->route('materials.index')
                    ->with('error', 'Import selesai dengan error: ' . implode(' | ', array_slice($errors, 0, 5)));
            }
            
            return redirect()->route('materials.index')
                ->with('success', "{$successCount} materials berhasil diimport");
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

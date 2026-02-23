<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Helpers\ExcelHelper;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;

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
        $suppliers = Supplier::all();
        
        $data = $suppliers->map(function($supplier) {
            return [
                $supplier->supplier_code,
                $supplier->supplier_name,
                $supplier->contact_person,
                $supplier->phone,
                $supplier->email,
                $supplier->address,
                $supplier->is_active ? 'Active' : 'Inactive',
            ];
        })->toArray();
        
        $headers = [
            'Supplier Code',
            'Supplier Name',
            'Contact Person',
            'Phone',
            'Email',
            'Address',
            'Status',
        ];
        
        return ExcelHelper::export($data, $headers, 'suppliers_' . date('YmdHis') . '.xlsx');
    }

    public function downloadTemplate()
    {
        $headers = [
            'supplier_code',
            'supplier_name',
            'contact_person',
            'phone',
            'email',
            'address',
            'city',
            'status'
        ];
        
        $columnWidths = [15, 30, 20, 15, 30, 40, 15, 12];
        
        $sampleData = [
            [
                'SUP-001',
                'PT Steel Indonesia',
                'Ahmad Santoso',
                '021-5551234',
                'ahmad@steelindonesia.com',
                'Jl. Industri No. 123, Jakarta',
                'Jakarta',
                'Active'
            ],
            [
                'SUP-002',
                'CV Metal Jaya',
                'Budi Wijaya',
                '022-8887654',
                'budi@metaljaya.com',
                'Jl. Raya Industri No. 45, Bandung',
                'Bandung',
                'Active'
            ],
            [
                'SUP-003',
                'PT Mitra Stamping',
                'Citra Lestari',
                '031-7778899',
                'citra@mitrastamping.com',
                'Jl. Perindustrian No. 78, Surabaya',
                'Surabaya',
                'Active'
            ],
        ];
        
        return ExcelHelper::createTemplate($headers, 'suppliers_import_template.xlsx', $columnWidths, $sampleData);
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
                if (empty($row['supplier_code']) && empty($row['supplier_name'])) {
                    continue;
                }
                
                // Validate row
                $validator = Validator::make($row, [
                    'supplier_code' => 'required|unique:suppliers,supplier_code',
                    'supplier_name' => 'required',
                    'email' => 'nullable|email',
                ], [
                    'supplier_code.required' => 'Kode supplier wajib diisi',
                    'supplier_code.unique' => 'Kode supplier sudah ada',
                    'supplier_name.required' => 'Nama supplier wajib diisi',
                    'email.email' => 'Format email tidak valid',
                ]);
                
                if ($validator->fails()) {
                    $errors[] = "Baris {$rowNumber}: " . implode(', ', $validator->errors()->all());
                    continue;
                }
                
                // Create supplier
                Supplier::create([
                    'supplier_code' => trim($row['supplier_code']),
                    'supplier_name' => trim($row['supplier_name']),
                    'contact_person' => isset($row['contact_person']) ? trim($row['contact_person']) : null,
                    'phone' => isset($row['phone']) ? trim($row['phone']) : null,
                    'email' => isset($row['email']) ? trim($row['email']) : null,
                    'address' => isset($row['address']) ? trim($row['address']) : null,
                    'city' => isset($row['city']) ? trim($row['city']) : null,
                    'is_active' => !isset($row['status']) || strtolower(trim($row['status'])) === 'active',
                ]);
                
                $successCount++;
            }
            
            if (count($errors) > 0) {
                return redirect()->route('suppliers.index')
                    ->with('error', 'Import selesai dengan error: ' . implode(' | ', array_slice($errors, 0, 5)));
            }
            
            return redirect()->route('suppliers.index')
                ->with('success', "{$successCount} suppliers berhasil diimport");
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

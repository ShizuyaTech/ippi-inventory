<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Helpers\ExcelHelper;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();
        
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('customer_code', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $customers = $query->latest()->paginate(20)->withQueryString();
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_code' => 'required|unique:customers',
            'customer_name' => 'required',
            'contact_person' => 'nullable',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'address' => 'nullable',
            'city' => 'nullable',
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Customer berhasil ditambahkan');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'customer_code' => 'required|unique:customers,customer_code,' . $customer->id,
            'customer_name' => 'required',
            'contact_person' => 'nullable',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'address' => 'nullable',
            'city' => 'nullable',
            'is_active' => 'boolean',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Customer berhasil diupdate');
    }

    public function destroy(Customer $customer)
    {
        try {
            $customer->delete();
            return redirect()->route('customers.index')
                ->with('success', 'Customer berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('customers.index')
                ->with('error', 'Customer tidak bisa dihapus karena memiliki transaksi');
        }
    }

    public function export()
    {
        $customers = Customer::all();
        
        $data = $customers->map(function($customer) {
            return [
                $customer->customer_code,
                $customer->customer_name,
                $customer->contact_person,
                $customer->phone,
                $customer->email,
                $customer->address,
                $customer->is_active ? 'Active' : 'Inactive',
            ];
        })->toArray();
        
        $headers = [
            'Customer Code',
            'Customer Name',
            'Contact Person',
            'Phone',
            'Email',
            'Address',
            'Status',
        ];
        
        return ExcelHelper::export($data, $headers, 'customers_' . date('YmdHis') . '.xlsx');
    }

    public function downloadTemplate()
    {
        $headers = [
            'customer_code',
            'customer_name',
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
                'CUST-001',
                'PT Industri Otomotif',
                'Dewi Kartika',
                '021-9998888',
                'dewi@otomotif.com',
                'Jl. Otomotif No. 999, Jakarta',
                'Jakarta',
                'Active'
            ],
            [
                'CUST-002',
                'CV Elektronik Maju',
                'Eko Prasetyo',
                '022-7776655',
                'eko@elektronikmaju.com',
                'Jl. Elektronik No. 88, Bandung',
                'Bandung',
                'Active'
            ],
        ];
        
        return ExcelHelper::createTemplate($headers, 'customers_import_template.xlsx', $columnWidths, $sampleData);
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
                if (empty($row['customer_code']) && empty($row['customer_name'])) {
                    continue;
                }
                
                // Validate row
                $validator = Validator::make($row, [
                    'customer_code' => 'required|unique:customers,customer_code',
                    'customer_name' => 'required',
                    'email' => 'nullable|email',
                ], [
                    'customer_code.required' => 'Kode customer wajib diisi',
                    'customer_code.unique' => 'Kode customer sudah ada',
                    'customer_name.required' => 'Nama customer wajib diisi',
                    'email.email' => 'Format email tidak valid',
                ]);
                
                if ($validator->fails()) {
                    $errors[] = "Baris {$rowNumber}: " . implode(', ', $validator->errors()->all());
                    continue;
                }
                
                // Create customer
                Customer::create([
                    'customer_code' => trim($row['customer_code']),
                    'customer_name' => trim($row['customer_name']),
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
                return redirect()->route('customers.index')
                    ->with('error', 'Import selesai dengan error: ' . implode(' | ', array_slice($errors, 0, 5)));
            }
            
            return redirect()->route('customers.index')
                ->with('success', "{$successCount} customers berhasil diimport");
        } catch (\Exception $e) {
            return redirect()->route('customers.index')
                ->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    public function exportPdf()
    {
        $customers = Customer::all();
        $pdf = Pdf::loadView('pdf.customers', compact('customers'));
        return $pdf->download('customers_' . date('YmdHis') . '.pdf');
    }
}

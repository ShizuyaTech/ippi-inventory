<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Exports\CustomersExport;
use App\Exports\CustomersTemplateExport;
use App\Imports\CustomersImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

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
        return Excel::download(new CustomersExport, 'customers_' . date('YmdHis') . '.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new CustomersTemplateExport, 'customers_import_template.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new CustomersImport, $request->file('file'));
            return redirect()->route('customers.index')
                ->with('success', 'Customers berhasil diimport');
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

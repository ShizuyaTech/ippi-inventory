<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomersExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Customer::all();
    }

    public function headings(): array
    {
        return [
            'Customer Code',
            'Customer Name',
            'Contact Person',
            'Phone',
            'Email',
            'Address',
            'Status',
        ];
    }

    public function map($customer): array
    {
        return [
            $customer->customer_code,
            $customer->customer_name,
            $customer->contact_person,
            $customer->phone,
            $customer->email,
            $customer->address,
            $customer->is_active ? 'Active' : 'Inactive',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

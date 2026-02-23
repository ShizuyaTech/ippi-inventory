<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SuppliersExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Supplier::all();
    }

    public function headings(): array
    {
        return [
            'Supplier Code',
            'Supplier Name',
            'Contact Person',
            'Phone',
            'Email',
            'Address',
            'Status',
        ];
    }

    public function map($supplier): array
    {
        return [
            $supplier->supplier_code,
            $supplier->supplier_name,
            $supplier->contact_person,
            $supplier->phone,
            $supplier->email,
            $supplier->address,
            $supplier->is_active ? 'Active' : 'Inactive',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

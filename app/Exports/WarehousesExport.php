<?php

namespace App\Exports;

use App\Models\Warehouse;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WarehousesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Warehouse::all();
    }

    public function headings(): array
    {
        return [
            'Warehouse Code',
            'Warehouse Name',
            'Location',
            'Capacity',
            'Status',
        ];
    }

    public function map($warehouse): array
    {
        return [
            $warehouse->warehouse_code,
            $warehouse->warehouse_name,
            $warehouse->location,
            $warehouse->capacity,
            $warehouse->is_active ? 'Active' : 'Inactive',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

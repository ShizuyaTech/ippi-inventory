<?php

namespace App\Exports;

use App\Models\Material;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MaterialsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Material::all();
    }

    public function headings(): array
    {
        return [
            'Material Code',
            'Material Name',
            'Description',
            'Category',
            'Unit of Measure',
            'Current Stock',
            'Status',
        ];
    }

    public function map($material): array
    {
        return [
            $material->material_code,
            $material->material_name,
            $material->description,
            $material->category,
            $material->unit_of_measure,
            $material->current_stock,
            $material->is_active ? 'Active' : 'Inactive',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

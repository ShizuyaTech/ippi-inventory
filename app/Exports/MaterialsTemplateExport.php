<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class MaterialsTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        // Return sample data with 3 examples
        return [
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
    }

    public function headings(): array
    {
        return [
            'material_code',
            'material_name',
            'description',
            'category',
            'unit_of_measure',
            'current_stock',
            'minimum_stock',
            'status'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,  // material_code
            'B' => 30,  // material_name
            'C' => 35,  // description
            'D' => 20,  // category
            'E' => 18,  // unit_of_measure
            'F' => 15,  // current_stock
            'G' => 15,  // minimum_stock
            'H' => 12,  // status
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style header row
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
        ]);

        // Add a note in a separate area (far below data)
        $noteRow = 10;
        $sheet->mergeCells('A' . $noteRow . ':H' . $noteRow);
        $sheet->setCellValue('A' . $noteRow, 'CATATAN: Hapus 3 baris contoh data di atas, lalu isi dengan data material Anda. Format: material_code (unique), material_name, category (RAW MATERIAL/FINISHED GOODS/SEMI-FINISHED/CONSUMABLE), unit_of_measure (PCS/KG/SHEET/etc)');
        
        $sheet->getStyle('A' . $noteRow)->applyFromArray([
            'font' => [
                'italic' => true,
                'size' => 9,
                'color' => ['rgb' => '666666'],
            ],
        ]);
        
        $sheet->getRowDimension($noteRow)->setRowHeight(30);
        $sheet->getStyle('A' . $noteRow)->getAlignment()->setWrapText(true);

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

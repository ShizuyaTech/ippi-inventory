<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SuppliersTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        return [
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
    }

    public function headings(): array
    {
        return [
            'supplier_code',
            'supplier_name',
            'contact_person',
            'phone',
            'email',
            'address',
            'city',
            'status'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,  // supplier_code
            'B' => 30,  // supplier_name
            'C' => 20,  // contact_person
            'D' => 15,  // phone
            'E' => 30,  // email
            'F' => 40,  // address
            'G' => 15,  // city
            'H' => 12,  // status
        ];
    }

    public function styles(Worksheet $sheet)
    {
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

        $noteRow = 10;
        $sheet->mergeCells('A' . $noteRow . ':H' . $noteRow);
        $sheet->setCellValue('A' . $noteRow, 'CATATAN: Hapus 3 baris contoh data di atas, lalu isi dengan data supplier Anda. Format: supplier_code (unique), supplier_name (required), status (Active/Inactive)');
        
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

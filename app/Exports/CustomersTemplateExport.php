<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CustomersTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        return [
            [
                'CUST-001',
                'PT Automotive Indonesia',
                'Dewi Sartika',
                '021-6667890',
                'dewi@automotiveid.com',
                'Jl. Gatot Subroto No. 88, Jakarta',
                'Jakarta',
                'Active'
            ],
            [
                'CUST-002',
                'CV Manufacturing Sejahtera',
                'Eko Prasetyo',
                '022-3334567',
                'eko@manufacturing.com',
                'Jl. Asia Afrika No. 12, Bandung',
                'Bandung',
                'Active'
            ],
            [
                'CUST-003',
                'PT Elektronik Prima',
                'Fitri Handayani',
                '031-9998877',
                'fitri@elektronikprima.com',
                'Jl. Pahlawan No. 56, Surabaya',
                'Surabaya',
                'Active'
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'customer_code',
            'customer_name',
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
            'A' => 15,  // customer_code
            'B' => 30,  // customer_name
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
        $sheet->setCellValue('A' . $noteRow, 'CATATAN: Hapus 3 baris contoh data di atas, lalu isi dengan data customer Anda. Format: customer_code (unique), customer_name (required), status (Active/Inactive)');
        
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

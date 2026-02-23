<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class WarehousesTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        return [
            [
                'WH-001',
                'Gudang Utama',
                'Jl. Industri No. 10, Jakarta',
                'Gudang utama untuk raw material',
                'Active'
            ],
            [
                'WH-002',
                'Gudang Produksi',
                'Jl. Raya Pabrik No. 5, Bekasi',
                'Gudang khusus work in process',
                'Active'
            ],
            [
                'WH-003',
                'Gudang Finished Goods',
                'Jl. Logistik No. 20, Tangerang',
                'Gudang penyimpanan produk jadi',
                'Active'
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'warehouse_code',
            'warehouse_name',
            'location',
            'description',
            'status'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,  // warehouse_code
            'B' => 30,  // warehouse_name
            'C' => 40,  // location
            'D' => 40,  // description
            'E' => 12,  // status
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:E1')->applyFromArray([
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
        $sheet->mergeCells('A' . $noteRow . ':E' . $noteRow);
        $sheet->setCellValue('A' . $noteRow, 'CATATAN: Hapus 3 baris contoh data di atas, lalu isi dengan data warehouse Anda. Format: warehouse_code (unique), warehouse_name (required), status (Active/Inactive)');
        
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

<?php

namespace App\Helpers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Response;

class ExcelHelper
{
    /**
     * Export data to Excel file
     *
     * @param array $data Array of arrays (rows)
     * @param array $headers Column headers
     * @param string $filename
     * @param array $columnWidths Optional column widths
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public static function export(array $data, array $headers, string $filename, array $columnWidths = [])
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $sheet->getStyle($column . '1')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]);
            $column++;
        }
        
        // Add data
        $row = 2;
        foreach ($data as $rowData) {
            $column = 'A';
            foreach ($rowData as $value) {
                $sheet->setCellValue($column . $row, $value);
                $column++;
            }
            $row++;
        }
        
        // Set column widths
        if (!empty($columnWidths)) {
            $column = 'A';
            foreach ($columnWidths as $width) {
                $sheet->getColumnDimension($column)->setWidth($width);
                $column++;
            }
        } else {
            // Auto-size columns
            foreach (range('A', $sheet->getHighestColumn()) as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
        }
        
        // Add borders
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ];
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow())->applyFromArray($styleArray);
        
        // Return download response
        $writer = new Xlsx($spreadsheet);
        
        return Response::streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
    
    /**
     * Import data from Excel file
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param int $headerRow The row number containing headers (1-based)
     * @return array Array of associative arrays with headers as keys
     */
    public static function import($file, int $headerRow = 1)
    {
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();
        
        if (empty($rows)) {
            return [];
        }
        
        // Get headers
        $headers = $rows[$headerRow - 1];
        
        // Convert headers to lowercase and replace spaces with underscores
        $headers = array_map(function($header) {
            return strtolower(str_replace(' ', '_', trim($header)));
        }, $headers);
        
        // Get data rows
        $data = [];
        for ($i = $headerRow; $i < count($rows); $i++) {
            $row = $rows[$i];
            
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }
            
            $rowData = [];
            foreach ($headers as $index => $header) {
                $rowData[$header] = $row[$index] ?? null;
            }
            $data[] = $rowData;
        }
        
        return $data;
    }
    
    /**
     * Create template Excel file
     *
     * @param array $headers Column headers
     * @param string $filename
     * @param array $columnWidths Optional column widths
     * @param array $sampleData Optional sample data rows
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public static function createTemplate(array $headers, string $filename, array $columnWidths = [], array $sampleData = [])
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $sheet->getStyle($column . '1')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]);
            $column++;
        }
        
        // Add sample data if provided
        if (!empty($sampleData)) {
            $row = 2;
            foreach ($sampleData as $rowData) {
                $column = 'A';
                foreach ($rowData as $value) {
                    $sheet->setCellValue($column . $row, $value);
                    $column++;
                }
                $row++;
            }
        }
        
        // Set column widths
        if (!empty($columnWidths)) {
            $column = 'A';
            foreach ($columnWidths as $width) {
                $sheet->getColumnDimension($column)->setWidth($width);
                $column++;
            }
        } else {
            // Auto-size columns
            foreach (range('A', $sheet->getHighestColumn()) as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
        }
        
        // Add borders
        $lastRow = !empty($sampleData) ? (count($sampleData) + 1) : 1;
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ];
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . $lastRow)->applyFromArray($styleArray);
        
        // Return download response
        $writer = new Xlsx($spreadsheet);
        
        return Response::streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}

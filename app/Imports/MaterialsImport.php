<?php

namespace App\Imports;

use App\Models\Material;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class MaterialsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row)
    {
        // Skip empty rows - check if all key fields are empty
        if (empty($row['material_code']) && empty($row['material_name']) && empty($row['unit_of_measure'])) {
            return null;
        }

        // Skip instruction/note rows (starts with INSTRUCTIONS, CATATAN, numbers, etc)
        $firstCol = trim($row['material_code'] ?? '');
        if (preg_match('/^(INSTRUCTIONS|CATATAN|NOTE|PANDUAN|PETUNJUK|\d+\.)/i', $firstCol)) {
            return null;
        }

        // Normalize unit of measure to uppercase
        $unitOfMeasure = isset($row['unit_of_measure']) ? strtoupper(trim($row['unit_of_measure'])) : null;
        
        return new Material([
            'material_code' => trim($row['material_code']),
            'material_name' => trim($row['material_name']),
            'description' => isset($row['description']) ? trim($row['description']) : null,
            'category' => isset($row['category']) ? trim($row['category']) : null,
            'unit_of_measure' => $unitOfMeasure,
            'current_stock' => isset($row['current_stock']) ? floatval($row['current_stock']) : 0,
            'minimum_stock' => isset($row['minimum_stock']) ? floatval($row['minimum_stock']) : 0,
            'location' => null,
            'is_active' => !isset($row['status']) || strtolower(trim($row['status'])) === 'active',
        ]);
    }

    public function rules(): array
    {
        return [
            'material_code' => 'required|unique:materials,material_code',
            'material_name' => 'required',
            'unit_of_measure' => 'required',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'material_code.required' => 'Kode material wajib diisi',
            'material_code.unique' => 'Kode material sudah ada di database',
            'material_name.required' => 'Nama material wajib diisi',
            'unit_of_measure.required' => 'Unit of measure wajib diisi',
        ];
    }
}

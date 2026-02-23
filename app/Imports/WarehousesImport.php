<?php

namespace App\Imports;

use App\Models\Warehouse;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class WarehousesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row)
    {
        // Skip empty rows
        if (empty($row['warehouse_code']) && empty($row['warehouse_name'])) {
            return null;
        }

        return new Warehouse([
            'warehouse_code' => trim($row['warehouse_code']),
            'warehouse_name' => trim($row['warehouse_name']),
            'location' => isset($row['location']) ? trim($row['location']) : null,
            'description' => isset($row['description']) ? trim($row['description']) : null,
            'is_active' => strtolower($row['status'] ?? 'active') === 'active',
        ]);
    }

    public function rules(): array
    {
        return [
            'warehouse_code' => 'required|unique:warehouses,warehouse_code',
            'warehouse_name' => 'required',
        ];
    }
}

<?php

namespace App\Imports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class SuppliersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row)
    {
        // Skip empty rows
        if (empty($row['supplier_code']) && empty($row['supplier_name'])) {
            return null;
        }

        return new Supplier([
            'supplier_code' => trim($row['supplier_code']),
            'supplier_name' => trim($row['supplier_name']),
            'contact_person' => isset($row['contact_person']) ? trim($row['contact_person']) : null,
            'phone' => isset($row['phone']) ? trim($row['phone']) : null,
            'email' => isset($row['email']) ? trim($row['email']) : null,
            'address' => isset($row['address']) ? trim($row['address']) : null,
            'city' => isset($row['city']) ? trim($row['city']) : null,
            'is_active' => strtolower($row['status'] ?? 'active') === 'active',
        ]);
    }

    public function rules(): array
    {
        return [
            'supplier_code' => 'required|unique:suppliers,supplier_code',
            'supplier_name' => 'required',
            'email' => 'nullable|email',
        ];
    }
}

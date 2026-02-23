<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class CustomersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row)
    {
        // Skip empty rows
        if (empty($row['customer_code']) && empty($row['customer_name'])) {
            return null;
        }

        return new Customer([
            'customer_code' => trim($row['customer_code']),
            'customer_name' => trim($row['customer_name']),
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
            'customer_code' => 'required|unique:customers,customer_code',
            'customer_name' => 'required',
            'email' => 'nullable|email',
        ];
    }
}

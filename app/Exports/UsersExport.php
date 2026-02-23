<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return User::all();
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Role',
            'Supplier',
            'Status',
            'Created At',
        ];
    }

    public function map($user): array
    {
        return [
            $user->name,
            $user->email,
            ucfirst($user->role),
            $user->supplier ? $user->supplier->supplier_name : '-',
            $user->is_active ? 'Active' : 'Inactive',
            $user->created_at->format('Y-m-d H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

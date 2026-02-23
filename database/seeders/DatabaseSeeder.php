<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Material;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Suppliers first (needed for supplier users)
        $suppliers = [
            ['supplier_code' => 'SUP001', 'supplier_name' => 'PT. Steel Indonesia', 'contact_person' => 'Budi Santoso', 'phone' => '021-12345678', 'email' => 'budi@steelindonesia.com', 'address' => 'Jl. Industri No. 123', 'city' => 'Jakarta'],
            ['supplier_code' => 'SUP002', 'supplier_name' => 'CV. Metal Jaya', 'contact_person' => 'Andi Wijaya', 'phone' => '021-87654321', 'email' => 'andi@metaljaya.com', 'address' => 'Jl. Metal No. 45', 'city' => 'Bekasi'],
            ['supplier_code' => 'SUP003', 'supplier_name' => 'PT. Prima Material', 'contact_person' => 'Siti Rahman', 'phone' => '021-55555555', 'email' => 'siti@primamaterial.com', 'address' => 'Jl. Prima No. 78', 'city' => 'Tangerang'],
        ];

        $supplierModels = [];
        foreach ($suppliers as $supplier) {
            $supplierModels[] = Supplier::create($supplier);
        }

        // Create Users with different roles
        // Admin User
        User::create([
            'name' => 'Admin',
            'email' => 'admin@materialcontrol.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Staff User
        User::create([
            'name' => 'Staff Warehouse',
            'email' => 'staff@materialcontrol.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);

        // Supplier Users (one for each supplier)
        User::create([
            'name' => 'Budi Santoso - PT. Steel Indonesia',
            'email' => 'supplier@steelindonesia.com',
            'password' => Hash::make('password'),
            'role' => 'supplier',
            'supplier_id' => $supplierModels[0]->id,
        ]);

        User::create([
            'name' => 'Andi Wijaya - CV. Metal Jaya',
            'email' => 'supplier@metaljaya.com',
            'password' => Hash::make('password'),
            'role' => 'supplier',
            'supplier_id' => $supplierModels[1]->id,
        ]);

        User::create([
            'name' => 'Siti Rahman - PT. Prima Material',
            'email' => 'supplier@primamaterial.com',
            'password' => Hash::make('password'),
            'role' => 'supplier',
            'supplier_id' => $supplierModels[2]->id,
        ]);

        // Create Customers
        $customers = [
            ['customer_code' => 'CUST001', 'customer_name' => 'PT. Automotive Indonesia', 'contact_person' => 'Rudi Hartono', 'phone' => '021-11111111', 'email' => 'rudi@automotive.com', 'address' => 'Jl. Auto No. 10', 'city' => 'Jakarta'],
            ['customer_code' => 'CUST002', 'customer_name' => 'CV. Elektronik Jaya', 'contact_person' => 'Dewi Lestari', 'phone' => '021-22222222', 'email' => 'dewi@elektronik.com', 'address' => 'Jl. Elektronik No. 20', 'city' => 'Bogor'],
            ['customer_code' => 'CUST003', 'customer_name' => 'PT. Manufaktur Prima', 'contact_person' => 'Agus Salim', 'phone' => '021-33333333', 'email' => 'agus@manufaktur.com', 'address' => 'Jl. Manufaktur No. 30', 'city' => 'Karawang'],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }

        // Create Warehouses
        $warehouses = [
            ['warehouse_code' => 'WH01', 'warehouse_name' => 'Warehouse Raw Material', 'description' => 'Gudang untuk raw material', 'location' => 'Area A'],
            ['warehouse_code' => 'WH02', 'warehouse_name' => 'Warehouse WIP', 'description' => 'Gudang untuk work in process', 'location' => 'Area B'],
            ['warehouse_code' => 'WH03', 'warehouse_name' => 'Warehouse Finished Goods', 'description' => 'Gudang untuk finished goods', 'location' => 'Area C'],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::create($warehouse);
        }

        // Create Materials
        $materials = [
            [
                'material_code' => 'RM-001',
                'material_name' => 'Steel Plate SPCC 0.8mm',
                'description' => 'Steel plate cold rolled 0.8mm thickness',
                'unit_of_measure' => 'KG',
                'category' => 'Raw Material',
                'minimum_stock' => 1000,
                'current_stock' => 2500,
                'location' => 'WH01-A-001',
            ],
            [
                'material_code' => 'RM-002',
                'material_name' => 'Steel Plate SPCC 1.0mm',
                'description' => 'Steel plate cold rolled 1.0mm thickness',
                'unit_of_measure' => 'KG',
                'category' => 'Raw Material',
                'minimum_stock' => 1000,
                'current_stock' => 1800,
                'location' => 'WH01-A-002',
            ],
            [
                'material_code' => 'RM-003',
                'material_name' => 'Aluminium Sheet 1mm',
                'description' => 'Aluminium sheet 1mm thickness',
                'unit_of_measure' => 'KG',
                'category' => 'Raw Material',
                'minimum_stock' => 500,
                'current_stock' => 300,
                'location' => 'WH01-B-001',
            ],
            [
                'material_code' => 'WIP-001',
                'material_name' => 'Bracket A - Stamped',
                'description' => 'Bracket part A after stamping process',
                'unit_of_measure' => 'PCS',
                'category' => 'WIP',
                'minimum_stock' => 500,
                'current_stock' => 750,
                'location' => 'WH02-A-001',
            ],
            [
                'material_code' => 'WIP-002',
                'material_name' => 'Bracket B - Stamped',
                'description' => 'Bracket part B after stamping process',
                'unit_of_measure' => 'PCS',
                'category' => 'WIP',
                'minimum_stock' => 500,
                'current_stock' => 850,
                'location' => 'WH02-A-002',
            ],
            [
                'material_code' => 'FG-001',
                'material_name' => 'Bracket Assembly Complete',
                'description' => 'Bracket assembly finished product',
                'unit_of_measure' => 'PCS',
                'category' => 'Finished Goods',
                'minimum_stock' => 200,
                'current_stock' => 450,
                'location' => 'WH03-A-001',
            ],
            [
                'material_code' => 'FG-002',
                'material_name' => 'Mounting Plate Assembly',
                'description' => 'Mounting plate finished product',
                'unit_of_measure' => 'PCS',
                'category' => 'Finished Goods',
                'minimum_stock' => 200,
                'current_stock' => 180,
                'location' => 'WH03-A-002',
            ],
            [
                'material_code' => 'CONS-001',
                'material_name' => 'Cutting Oil',
                'description' => 'Cutting oil for stamping machine',
                'unit_of_measure' => 'ROLL',
                'category' => 'Consumables',
                'minimum_stock' => 20,
                'current_stock' => 35,
                'location' => 'WH01-C-001',
            ],
            [
                'material_code' => 'CONS-002',
                'material_name' => 'Grinding Disc 4"',
                'description' => 'Grinding disc 4 inch for finishing',
                'unit_of_measure' => 'PCS',
                'category' => 'Consumables',
                'minimum_stock' => 50,
                'current_stock' => 75,
                'location' => 'WH01-C-002',
            ],
            [
                'material_code' => 'TOOL-001',
                'material_name' => 'Stamping Die Set A',
                'description' => 'Die set for bracket A stamping',
                'unit_of_measure' => 'PCS',
                'category' => 'Tools',
                'minimum_stock' => 2,
                'current_stock' => 3,
                'location' => 'TOOL-ROOM-A',
            ],
        ];

        foreach ($materials as $material) {
            Material::create($material);
        }
    }
}

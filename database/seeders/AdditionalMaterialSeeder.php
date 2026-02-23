<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdditionalMaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Additional Raw Materials (SHEET)
        DB::table('materials')->insert([
            [
                'material_code' => 'RM-004',
                'material_name' => 'Steel Sheet Cold Rolled 1.2mm',
                'description' => 'Cold rolled steel sheet for heavy duty stamping',
                'unit_of_measure' => 'SHEET',
                'category' => 'Raw Material',
                'minimum_stock' => 50,
                'current_stock' => 120,
                'location' => 'Warehouse A - Rack 1',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'material_code' => 'RM-005',
                'material_name' => 'Stainless Steel Sheet 0.8mm',
                'description' => 'Stainless steel sheet 304 grade',
                'unit_of_measure' => 'SHEET',
                'category' => 'Raw Material',
                'minimum_stock' => 30,
                'current_stock' => 85,
                'location' => 'Warehouse A - Rack 2',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'material_code' => 'RM-006',
                'material_name' => 'Galvanized Steel Coil 0.5mm',
                'description' => 'Zinc coated steel coil for small parts',
                'unit_of_measure' => 'ROLL',
                'category' => 'Raw Material',
                'minimum_stock' => 10,
                'current_stock' => 25,
                'location' => 'Warehouse A - Rack 3',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Additional Finished Parts
        DB::table('materials')->insert([
            [
                'material_code' => 'FG-003',
                'material_name' => 'Washer M8 Stamped',
                'description' => 'Stamped washer M8 size',
                'unit_of_measure' => 'PCS',
                'category' => 'Finished Goods',
                'minimum_stock' => 5000,
                'current_stock' => 12000,
                'location' => 'Warehouse B - Bin 10',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'material_code' => 'FG-004',
                'material_name' => 'Metal Clip Type A',
                'description' => 'Spring clip for automotive',
                'unit_of_measure' => 'PCS',
                'category' => 'Finished Goods',
                'minimum_stock' => 3000,
                'current_stock' => 8500,
                'location' => 'Warehouse B - Bin 11',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'material_code' => 'FG-005',
                'material_name' => 'Connector Plate Small',
                'description' => 'Small connector plate for frame assembly',
                'unit_of_measure' => 'PCS',
                'category' => 'Finished Goods',
                'minimum_stock' => 2000,
                'current_stock' => 4500,
                'location' => 'Warehouse B - Bin 12',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'material_code' => 'FG-006',
                'material_name' => 'Reinforcement Bracket',
                'description' => 'Heavy duty reinforcement bracket',
                'unit_of_measure' => 'PCS',
                'category' => 'Finished Goods',
                'minimum_stock' => 1000,
                'current_stock' => 2800,
                'location' => 'Warehouse B - Bin 13',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

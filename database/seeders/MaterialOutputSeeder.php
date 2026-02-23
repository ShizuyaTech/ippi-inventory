<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaterialOutputSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only insert if material_outputs is empty
        if (DB::table('material_outputs')->count() > 0) {
            $this->command->info('Material outputs already exist. Adding only new outputs...');
            
            // Add only new outputs for new materials
            DB::table('material_outputs')->insertOrIgnore([
                // Steel Sheet Cold Rolled 1.2mm (RM-004) - 1 SHEET produces multiple items
                [
                    'source_material_id' => 11, // RM-004: Steel Sheet Cold Rolled 1.2mm
                    'output_material_id' => 17, // FG-006: Reinforcement Bracket
                    'quantity_per_unit' => 2.00,
                    'notes' => '1 SHEET (1200x2400mm) menghasilkan 2 bracket besar',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'source_material_id' => 11, // RM-004: Steel Sheet Cold Rolled 1.2mm
                    'output_material_id' => 16, // FG-005: Connector Plate Small
                    'quantity_per_unit' => 12.00,
                    'notes' => 'Sisa material dari bracket bisa jadi 12 connector plate',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                
                // Stainless Steel Sheet 0.8mm (RM-005)
                [
                    'source_material_id' => 12, // RM-005: Stainless Steel Sheet 0.8mm
                    'output_material_id' => 15, // FG-004: Metal Clip Type A
                    'quantity_per_unit' => 48.00,
                    'notes' => '1 SHEET menghasilkan 48 spring clips',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'source_material_id' => 12, // RM-005: Stainless Steel Sheet 0.8mm
                    'output_material_id' => 14, // FG-003: Washer M8 Stamped
                    'quantity_per_unit' => 150.00,
                    'notes' => 'Sisa lubang tengah clips bisa jadi 150 washer M8',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                
                // Galvanized Steel Coil 0.5mm (RM-006)
                [
                    'source_material_id' => 13, // RM-006: Galvanized Steel Coil 0.5mm
                    'output_material_id' => 14, // FG-003: Washer M8 Stamped
                    'quantity_per_unit' => 800.00,
                    'notes' => '1 ROLL coil bisa menghasilkan 800 washer M8',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'source_material_id' => 13, // RM-006: Galvanized Steel Coil 0.5mm
                    'output_material_id' => 16, // FG-005: Connector Plate Small
                    'quantity_per_unit' => 120.00,
                    'notes' => 'Material tipis cocok untuk connector plate kecil',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
            
            return;
        }
        
        DB::table('material_outputs')->insert([
            // Steel Plate SPCC 0.8mm (RM-001) can produce Bracket A and Bracket B
            [
                'source_material_id' => 1, // Steel Plate SPCC 0.8mm
                'output_material_id' => 4, // Bracket A - Stamped
                'quantity_per_unit' => 3.00, // 1 KG steel can produce 3 PCS Bracket A
                'notes' => 'Standard stamping yield with 15% scrap',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'source_material_id' => 1, // Steel Plate SPCC 0.8mm
                'output_material_id' => 5, // Bracket B - Stamped
                'quantity_per_unit' => 24.00, // 1 KG steel can produce 24 PCS Bracket B
                'notes' => 'Small parts, higher yield per kg',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Steel Plate SPCC 1.0mm (RM-002) can produce different quantities
            [
                'source_material_id' => 2, // Steel Plate SPCC 1.0mm
                'output_material_id' => 4, // Bracket A - Stamped
                'quantity_per_unit' => 2.50, // 1 KG thicker steel produces fewer parts
                'notes' => 'Thicker material, lower yield',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'source_material_id' => 2, // Steel Plate SPCC 1.0mm
                'output_material_id' => 7, // Mounting Plate Assembly
                'quantity_per_unit' => 4.00, // 1 KG produces 4 PCS Mounting Plate
                'notes' => 'Medium size parts',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Aluminium Sheet (RM-003) lighter material, different yield
            [
                'source_material_id' => 3, // Aluminium Sheet 1mm
                'output_material_id' => 5, // Bracket B - Stamped
                'quantity_per_unit' => 32.00, // 1 KG aluminium produces more due to lighter weight
                'notes' => 'Aluminium is lighter, higher piece count per kg',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Steel Sheet Cold Rolled 1.2mm (RM-004) - 1 SHEET produces multiple items
            [
                'source_material_id' => 11, // Steel Sheet Cold Rolled 1.2mm
                'output_material_id' => 17, // Reinforcement Bracket (FG-006)
                'quantity_per_unit' => 2.00, // 1 SHEET produces 2 PCS heavy bracket
                'notes' => '1 SHEET (1200x2400mm) menghasilkan 2 bracket besar',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'source_material_id' => 11, // Steel Sheet Cold Rolled 1.2mm
                'output_material_id' => 15, // Connector Plate Small (FG-005)
                'quantity_per_unit' => 12.00, // 1 SHEET produces 12 PCS small plates
                'notes' => 'Sisa material dari bracket bisa jadi 12 connector plate',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Stainless Steel Sheet 0.8mm (RM-005) - 1 SHEET produces multiple items
            [
                'source_material_id' => 12, // Stainless Steel Sheet 0.8mm
                'output_material_id' => 14, // Metal Clip Type A (FG-004)
                'quantity_per_unit' => 48.00, // 1 SHEET produces 48 PCS clips
                'notes' => '1 SHEET menghasilkan 48 spring clips',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'source_material_id' => 12, // Stainless Steel Sheet 0.8mm
                'output_material_id' => 13, // Washer M8 (FG-003)
                'quantity_per_unit' => 150.00, // 1 SHEET produces 150 PCS washers
                'notes' => 'Sisa lubang tengah clips bisa jadi 150 washer M8',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Galvanized Steel Coil 0.5mm (RM-006) - 1 ROLL produces multiple items
            [
                'source_material_id' => 13, // Galvanized Steel Coil 0.5mm
                'output_material_id' => 13, // Washer M8 (FG-003)
                'quantity_per_unit' => 800.00, // 1 ROLL produces 800 PCS washers
                'notes' => '1 ROLL coil bisa menghasilkan 800 washer M8',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'source_material_id' => 13, // Galvanized Steel Coil 0.5mm
                'output_material_id' => 15, // Connector Plate Small (FG-005)
                'quantity_per_unit' => 120.00, // 1 ROLL produces 120 PCS plates
                'notes' => 'Material tipis cocok untuk connector plate kecil',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

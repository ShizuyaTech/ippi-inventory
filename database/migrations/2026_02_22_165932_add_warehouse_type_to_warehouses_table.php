<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('warehouses', function (Blueprint $table) {
            $table->enum('warehouse_type', ['RAW_MATERIAL', 'WIP', 'FINISHED_GOODS', 'CONSUMABLES', 'TOOLS', 'GENERAL'])
                  ->nullable()
                  ->after('warehouse_name')
                  ->comment('Type of warehouse for automatic material routing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouses', function (Blueprint $table) {
            $table->dropColumn('warehouse_type');
        });
    }
};

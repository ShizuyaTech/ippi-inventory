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
        Schema::table('production_order_outputs', function (Blueprint $table) {
            $table->decimal('qty_ng', 15, 2)->default(0)->after('actual_quantity')->comment('Not Good / Defect quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_order_outputs', function (Blueprint $table) {
            $table->dropColumn('qty_ng');
        });
    }
};

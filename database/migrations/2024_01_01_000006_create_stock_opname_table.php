<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opname', function (Blueprint $table) {
            $table->id();
            $table->string('opname_number')->unique();
            $table->date('opname_date');
            $table->foreignId('material_id')->constrained()->onDelete('restrict');
            $table->foreignId('warehouse_id')->nullable()->constrained()->onDelete('restrict');
            $table->decimal('system_stock', 15, 2);
            $table->decimal('physical_stock', 15, 2);
            $table->decimal('difference', 15, 2);
            $table->text('notes')->nullable();
            $table->enum('status', ['DRAFT', 'APPROVED', 'POSTED'])->default('DRAFT');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opname');
    }
};

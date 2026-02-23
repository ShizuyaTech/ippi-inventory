<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->date('transaction_date');
            $table->enum('transaction_type', ['IN', 'OUT', 'ADJUSTMENT', 'OPNAME']);
            $table->foreignId('material_id')->constrained()->onDelete('restrict');
            $table->foreignId('warehouse_id')->nullable()->constrained()->onDelete('restrict');
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('restrict');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('restrict');
            $table->decimal('quantity', 15, 2);
            $table->decimal('price', 15, 2)->default(0);
            $table->string('reference_number')->nullable(); // PO, DO, etc
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};

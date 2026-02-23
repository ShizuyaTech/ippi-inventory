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
        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->unique();
            $table->foreignId('source_material_id')->constrained('materials');
            $table->decimal('source_quantity', 15, 2);
            $table->string('production_line')->nullable();
            $table->date('planned_start_date');
            $table->datetime('actual_start_date')->nullable();
            $table->datetime('actual_complete_date')->nullable();
            $table->enum('status', ['DRAFT', 'PROCESSING', 'COMPLETED', 'CANCELLED'])->default('DRAFT');
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_orders');
    }
};

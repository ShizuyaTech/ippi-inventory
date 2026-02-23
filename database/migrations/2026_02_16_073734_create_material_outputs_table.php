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
        Schema::create('material_outputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_material_id')->constrained('materials')->onDelete('cascade');
            $table->foreignId('output_material_id')->constrained('materials')->onDelete('cascade');
            $table->decimal('quantity_per_unit', 15, 2); // Berapa PCS output dari 1 unit source
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Prevent duplicate entries
            $table->unique(['source_material_id', 'output_material_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_outputs');
    }
};

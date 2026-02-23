<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialOutput extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_material_id',
        'output_material_id',
        'quantity_per_unit',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'quantity_per_unit' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationship ke material source (raw material)
    public function sourceMaterial()
    {
        return $this->belongsTo(Material::class, 'source_material_id');
    }

    // Relationship ke material output (finished part/WIP)
    public function outputMaterial()
    {
        return $this->belongsTo(Material::class, 'output_material_id');
    }
}

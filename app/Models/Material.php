<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_code',
        'material_name',
        'description',
        'unit_of_measure',
        'category',
        'minimum_stock',
        'current_stock',
        'location',
        'is_active',
    ];

    protected $casts = [
        'minimum_stock' => 'decimal:2',
        'current_stock' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function transactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function stockOpname()
    {
        return $this->hasMany(StockOpname::class);
    }

    // Material outputs dari material ini (sebagai source)
    public function outputs()
    {
        return $this->hasMany(MaterialOutput::class, 'source_material_id');
    }

    // Material yang menggunakan material ini sebagai output
    public function sources()
    {
        return $this->hasMany(MaterialOutput::class, 'output_material_id');
    }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }
}

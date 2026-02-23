<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionOrderOutput extends Model
{
    protected $fillable = [
        'production_order_id',
        'output_material_id',
        'expected_quantity',
        'actual_quantity',
        'notes',
    ];

    protected $casts = [
        'expected_quantity' => 'decimal:2',
        'actual_quantity' => 'decimal:2',
    ];

    // Relationships
    public function productionOrder()
    {
        return $this->belongsTo(ProductionOrder::class);
    }

    public function outputMaterial()
    {
        return $this->belongsTo(Material::class, 'output_material_id');
    }

    // Helper methods
    public function getYieldRate()
    {
        if (!$this->actual_quantity || $this->expected_quantity == 0) {
            return 0;
        }
        return ($this->actual_quantity / $this->expected_quantity) * 100;
    }

    public function getScrapQuantity()
    {
        if (!$this->actual_quantity) {
            return 0;
        }
        return $this->expected_quantity - $this->actual_quantity;
    }
}

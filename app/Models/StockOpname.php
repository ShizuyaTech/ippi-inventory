<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    use HasFactory;

    protected $table = 'stock_opname';

    protected $fillable = [
        'opname_number',
        'opname_date',
        'material_id',
        'warehouse_id',
        'system_stock',
        'physical_stock',
        'difference',
        'notes',
        'status',
        'user_id',
    ];

    protected $casts = [
        'opname_date' => 'date',
        'system_stock' => 'decimal:2',
        'physical_stock' => 'decimal:2',
        'difference' => 'decimal:2',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($opname) {
            $opname->difference = $opname->physical_stock - $opname->system_stock;
        });

        static::updated(function ($opname) {
            if ($opname->status === 'POSTED' && $opname->getOriginal('status') !== 'POSTED') {
                // Create adjustment transaction
                StockTransaction::create([
                    'transaction_number' => 'ADJ-' . $opname->opname_number,
                    'transaction_date' => $opname->opname_date,
                    'transaction_type' => 'ADJUSTMENT',
                    'material_id' => $opname->material_id,
                    'warehouse_id' => $opname->warehouse_id,
                    'quantity' => $opname->difference,
                    'reference_number' => $opname->opname_number,
                    'notes' => 'Stock Opname Adjustment',
                    'user_id' => $opname->user_id,
                ]);
            }
        });
    }
}

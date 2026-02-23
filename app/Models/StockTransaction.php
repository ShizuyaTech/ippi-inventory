<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_number',
        'transaction_date',
        'transaction_type',
        'material_id',
        'warehouse_id',
        'supplier_id',
        'customer_id',
        'quantity',
        'price',
        'reference_number',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'quantity' => 'decimal:2',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($transaction) {
            $material = $transaction->material;
            
            if ($transaction->transaction_type === 'IN') {
                $material->current_stock += $transaction->quantity;
            } elseif ($transaction->transaction_type === 'OUT') {
                $material->current_stock -= $transaction->quantity;
            } elseif ($transaction->transaction_type === 'ADJUSTMENT') {
                $material->current_stock += $transaction->quantity;
            }
            
            $material->save();
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionOrder extends Model
{
    protected $fillable = [
        'po_number',
        'source_material_id',
        'source_quantity',
        'production_line',
        'planned_start_date',
        'actual_start_date',
        'actual_complete_date',
        'status',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'source_quantity' => 'decimal:2',
        'planned_start_date' => 'date',
        'actual_start_date' => 'datetime',
        'actual_complete_date' => 'datetime',
    ];

    // Relationships
    public function sourceMaterial()
    {
        return $this->belongsTo(Material::class, 'source_material_id');
    }

    public function outputs()
    {
        return $this->hasMany(ProductionOrderOutput::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper methods
    public function canStart()
    {
        return $this->status === 'DRAFT';
    }

    public function canComplete()
    {
        return $this->status === 'PROCESSING';
    }

    public function canCancel()
    {
        return in_array($this->status, ['DRAFT', 'PROCESSING']);
    }

    public function isCompleted()
    {
        return $this->status === 'COMPLETED';
    }

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'DRAFT' => 'bg-gray-100 text-gray-800',
            'PROCESSING' => 'bg-blue-100 text-blue-800',
            'COMPLETED' => 'bg-green-100 text-green-800',
            'CANCELLED' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    // Warehouse Type Constants
    const TYPE_RAW_MATERIAL = 'RAW_MATERIAL';
    const TYPE_WIP = 'WIP';
    const TYPE_FINISHED_GOODS = 'FINISHED_GOODS';
    const TYPE_CONSUMABLES = 'CONSUMABLES';
    const TYPE_TOOLS = 'TOOLS';
    const TYPE_GENERAL = 'GENERAL';

    protected $fillable = [
        'warehouse_code',
        'warehouse_name',
        'warehouse_type',
        'description',
        'location',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get warehouse by type
     */
    public static function getByType(string $type): ?self
    {
        return self::where('warehouse_type', $type)
                   ->where('is_active', true)
                   ->first();
    }

    /**
     * Get warehouse for material category
     */
    public static function getForMaterialCategory(string $category): ?self
    {
        $typeMapping = [
            'Raw Material' => self::TYPE_RAW_MATERIAL,
            'WIP' => self::TYPE_WIP,
            'Finished Goods' => self::TYPE_FINISHED_GOODS,
            'Consumables' => self::TYPE_CONSUMABLES,
            'Tools' => self::TYPE_TOOLS,
        ];

        $warehouseType = $typeMapping[$category] ?? self::TYPE_GENERAL;
        
        return self::getByType($warehouseType) ?? self::getByType(self::TYPE_GENERAL);
    }

    public function transactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function stockOpname()
    {
        return $this->hasMany(StockOpname::class);
    }
}

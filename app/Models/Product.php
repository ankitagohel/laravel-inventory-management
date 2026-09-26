<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'sku',
        'barcode',
        'name',
        'description',
        'category_id',
        'supplier_id',
        'cost_price',
        'selling_price',
        'current_stock',
        'min_stock_alert',
        'unit',
        'warehouse_location',
        'status',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'current_stock' => 'integer',
        'min_stock_alert' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class)->latest();
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->current_stock <= 0) {
            return 'out_of_stock';
        }
        if ($this->current_stock <= $this->min_stock_alert) {
            return 'low_stock';
        }
        return 'in_stock';
    }

    public function getStockStatusLabelAttribute(): string
    {
        return match($this->stock_status) {
            'out_of_stock' => 'Out of Stock',
            'low_stock' => 'Low Stock',
            'in_stock' => 'In Stock',
        };
    }

    public function getStockStatusBadgeClassAttribute(): string
    {
        return match($this->stock_status) {
            'out_of_stock' => 'badge-danger',
            'low_stock' => 'badge-warning',
            'in_stock' => 'badge-success',
        };
    }

    public function getTotalCostValueAttribute(): float
    {
        return (float) ($this->current_stock * $this->cost_price);
    }

    public function getTotalRetailValueAttribute(): float
    {
        return (float) ($this->current_stock * $this->selling_price);
    }
}

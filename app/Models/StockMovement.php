<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'previous_stock',
        'resulting_stock',
        'reason',
        'reference_no',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'previous_stock' => 'integer',
        'resulting_stock' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

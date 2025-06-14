<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'stock',
        'low_stock',
        'status',
        'is_active',
    ];
    public function stockDeductions()
    {
        return $this->hasMany(OrderStockDeduction::class);
    }

    public function stockOrders()
    {
        return $this->hasMany(StockOrder::class);
    }
}

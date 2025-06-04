<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStockDeduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'stock_id',
        'quantity_deducted',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}
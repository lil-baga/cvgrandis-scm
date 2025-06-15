<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ServiceRecipe extends Model
{
    protected $fillable = [
        'service_code', 'stock_id', 'quantity_per_unit', 'unit_of_measure'
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}
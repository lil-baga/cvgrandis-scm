<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOrder extends Model
{
    use HasFactory;
    protected $fillable = [
        'stock_id', 'user_id', 'quantity_requested', 'status', 
        'fulfilled_by', 'fulfilled_at'
    ];

    // Relasi ke tabel stocks
    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    // Relasi ke user yang memesan (Administrator)
    public function requester()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke user yang memenuhi (Supplier)
    public function fulfiller()
    {
        return $this->belongsTo(User::class, 'fulfilled_by');
    }
}
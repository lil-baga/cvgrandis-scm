<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'service',
        'description',
        'image_ref',
        'status',
        'original_filename',
        'mime_type',
        'path'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getServiceDisplayNameAttribute(): string
    {
        $serviceCode = $this->attributes['service'] ?? '';

        switch (strtolower($serviceCode)) {
            case 'neon':
                return 'Neon Box';
            case 'backdrop':
                return 'Backdrop Acara';
            case 'interior':
                return 'Design Interior';
            case 'lettering':
                return 'Letter Akrilik & Stainless';
            case 'event':
                return 'Event Organizer & RnD';
            case '':
                return 'Layanan Belum Ditentukan';
            default:
                return ('Layanan Lain');
        }
    }

    public function stockDeductions()
    {
        return $this->hasMany(OrderStockDeduction::class)->with('stock'); // Eager load info stoknya
    }
}

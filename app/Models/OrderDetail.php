<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = ['order_id','order_no','room_id','checkin_date','checkout_date','adult','children','subtotal'];
    protected $casts = [
        'checkin_date' => 'date', 
        'checkout_date' => 'date',
    ];

    public function room() {
        return $this->belongsTo(Room::class,'room_id');
    }
}

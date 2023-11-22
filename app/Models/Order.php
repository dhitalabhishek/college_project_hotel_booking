<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $casts = [
        'booking_date' => 'date'
    ];

    public function order_details()
    {
        return $this->hasOne(OrderDetail::class,'order_id');
    }
}

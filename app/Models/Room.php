<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    public function rRoomPhoto()
    {
        return $this->hasMany(RoomPhoto::class);
    }

    public function bookedRoom()
    {
        return $this->hasMany(BookedRoom::class,'room_id');
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class,'room_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use SoftDeletes;

    protected $fillable = ['number', 'capacity', 'price', 'description', 'floor_id', 'created_by'];

    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function images()
    {
        return $this->hasMany(RoomImage::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}


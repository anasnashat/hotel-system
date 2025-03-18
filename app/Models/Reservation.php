<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reservation_code', 'client_id', 'room_id', 'check_in_date', 'check_out_date',
        'accompany_number', 'price_at_booking', 'payment_intent_id', 'payment_method_id',
        'status'
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }


}

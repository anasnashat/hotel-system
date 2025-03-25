<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use function Pest\Laravel\patch;

class Reservation extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'reservations';
    protected $primaryKey = 'id';
    protected $fillable = [
        'client_id',
        'room_id',
        'is_reserved',
        'accompany_number',
        'price_at_booking',
        'payment_intent_id',
        'payment_method_id',
        'status',
    ];

    public $timestamps = true;

    // Relationship with Room
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    // Relationship with Client (User)
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Check if the room is reserved.
     *
     * @param int $roomId
     * @return bool
     */

    public function scopeIsReserved(Builder $query): Builder
    {
        return $query->where('is_reserved', true);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public static function boot()
    {
        parent::boot();
        self::creating(function ($reservation) {
            $reservation->reservation_code = 'RES-' . uniqid();

        });
    }
}

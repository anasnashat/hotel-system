<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = ['reservation_id', 'payment_intent_id', 'payment_method_id'];


    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }
}

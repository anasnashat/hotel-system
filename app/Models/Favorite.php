<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorite extends Model
{
    use HasFactory;

    protected $table = 'favorites';
    protected $primaryKey = 'id';

    protected $fillable = [
        'room_id',
    ];

    public $timestamps = true;
    public function user(): belongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function room(): belongsTo
    {
        return $this->belongsTo(Room::class);
    }
    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($favorite) {
            if (auth()->check()) {
                $favorite->user_id = auth()->id();
            }
        });
    }

}

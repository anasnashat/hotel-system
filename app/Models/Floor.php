<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Floor extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'number'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($floor) {
            // Automatically set created_by to the authenticated user's ID
            $floor->created_by = auth()->id();
        });
    }
}

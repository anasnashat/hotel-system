<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use phpDocumentor\Reflection\Types\This;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Room extends Model implements HasMedia
{
    use SoftDeletes;
    use InteractsWithMedia;


    // auth()->id
    protected $fillable = ['capacity', 'price', 'description', 'floor_id'];

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
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('rooms_image')
            ->useDisk('public')
            ->useFallbackPath(public_path('images/default.jpg'))
            ->useFallbackUrl(url('images/default.jpg'));
    }
    public function registerMediaConversions($media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(200)
            ->height(200)
            ->sharpen(10);
    }
    public function getMediaDirectoryAttribute()
    {
        return 'rooms/' . $this->number;
    }

    protected static function generateUniqueFloorNumber(): int
    {
        $maxNumber = Room::max('number');
        $randomNumber = rand(1, 1000);
        return $maxNumber ? $maxNumber + $randomNumber : 1000;
    }
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($floor) {
            // Automatically set created_by to the authenticated user's ID
            $floor->created_by = auth()->id();
            $floor->number =  self::generateUniqueFloorNumber();
        });
    }
}


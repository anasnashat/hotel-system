<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Cog\Contracts\Ban\Bannable as BannableContract;
use Cog\Laravel\Ban\Traits\Bannable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements BannableContract
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;
    use Bannable;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'avatar_image', 'gender', 'last_login_at'
    ];

    protected $hidden = ['password', 'remember_token'];

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }


    public function approvedUsers()
    {
        return $this->hasMany(UserProfile::class, 'approved_by');
    }

    public function createdFloors()
    {
        return $this->hasMany(Floor::class, 'created_by');
    }

    public function createdRooms()
    {
        return $this->hasMany(Room::class, 'created_by');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'client_id');
    }





    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    

}

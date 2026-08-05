<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    // Roles
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }


    // Addresses
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }


    // Carts
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }


    // Orders
    public function orders()
    {
        return $this->hasMany(Order::class);
    }


    // Reviews
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }


    // Internal Notifications
    public function internalNotifications()
    {
        return $this->hasMany(InternalNotification::class);
    }
}

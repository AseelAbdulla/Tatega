<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * Guard used by Spatie Permission.
     */
    protected $guard_name = 'sanctum';

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

    /**
     * Default guard used by Spatie Permission.
     */
    public function getDefaultGuardName(): string
    {
        return 'sanctum';
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

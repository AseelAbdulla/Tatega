<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Address; // تصحيح حرف A الكبير هنا

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

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

    public function getDefaultGuardName(): string
    {
        return 'sanctum';
    }

    /**
     * العلاقة بالمفرد لتتطابق مع الـ Controller
     */
    public function address()
    {
        return $this->hasOne(Address::class);
    }

    /**
     * اختياري: إبقاء الجمع أيضاً لمنع كسر أي كود آخر في المشروع
     */
    public function addresses()
    {
        return $this->hasOne(Address::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function internalNotifications()
    {
        return $this->hasMany(InternalNotification::class);
    }
}

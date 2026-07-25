<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

 
class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    // الأدوار (Roles) - علاقة Many-to-Many عبر جدول pivot `role_user`
    public function roles()
    
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    // العناوين
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    // السلات
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    // الطلبات
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // التقييمات
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // الإشعارات الداخلية
    public function internalNotifications()
    {
        return $this->hasMany(InternalNotification::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Review;
use App\Models\InternalNotification;
use App\Models\InternationalImportRequest;
use App\Models\PaymentMethod;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /*
    |--------------------------------------------------------------------------
    | SPATIE PERMISSION GUARD
    |--------------------------------------------------------------------------
    */

    protected $guard_name = 'sanctum';

    /*
    |--------------------------------------------------------------------------
    | FILLABLE
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
    'name',
    'email',
    'phone',
    'customer_type',
    'password',
    'status',
];

    /*
    |--------------------------------------------------------------------------
    | HIDDEN
    |--------------------------------------------------------------------------
    */

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /*
    |--------------------------------------------------------------------------
    | CASTS
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | DEFAULT GUARD
    |--------------------------------------------------------------------------
    |
    | Spatie Permission يعمل باستخدام Sanctum.
    |
    */

    public function getDefaultGuardName(): string
    {
        return 'sanctum';
    }

    /*
    |--------------------------------------------------------------------------
    | ADDRESSES
    |--------------------------------------------------------------------------
    */

    public function addresses(): HasMany
    {
        return $this->hasMany(
            Address::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PAYMENT METHODS
    |--------------------------------------------------------------------------
    */

    public function paymentMethods(): HasMany
    {
        return $this->hasMany(
            PaymentMethod::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | INTERNATIONAL IMPORT REQUESTS
    |--------------------------------------------------------------------------
    |
    | طلبات اعتماد المستورد الخاصة بالعميل.
    |
    */

    public function internationalImportRequests(): HasMany
    {
        return $this->hasMany(
            InternationalImportRequest::class,
            'user_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CARTS
    |--------------------------------------------------------------------------
    */

    public function carts(): HasMany
    {
        return $this->hasMany(
            Cart::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ORDERS
    |--------------------------------------------------------------------------
    */

    public function orders(): HasMany
    {
        return $this->hasMany(
            Order::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | REVIEWS
    |--------------------------------------------------------------------------
    */

    public function reviews(): HasMany
    {
        return $this->hasMany(
            Review::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | INTERNAL NOTIFICATIONS
    |--------------------------------------------------------------------------
    */

    public function internalNotifications(): HasMany
    {
        return $this->hasMany(
            InternalNotification::class
        );
    }
}


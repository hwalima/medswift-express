<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'organisation',
        'license_number',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    public function clientShipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'client_id');
    }

    public function courierShipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'courier_id');
    }

    public function courierRoutes(): HasMany
    {
        return $this->hasMany(CourierRoute::class, 'driver_id');
    }

    public function isAdmin(): bool   { return $this->role === 'admin'; }
    public function isCourier(): bool { return $this->role === 'courier'; }
    public function isClient(): bool  { return $this->role === 'client'; }
}


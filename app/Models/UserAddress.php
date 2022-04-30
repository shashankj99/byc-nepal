<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $guarded = ["id"];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customerBins(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CustomerBin::class);
    }

    public function pickups(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Pickup::class);
    }

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function driverPickups(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DriverPickup::class);
    }
}

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Pickup extends Model
{
    protected $guarded = ["id"];

    public function getPickupDateFormattedAttribute(): ?string
    {
        if ($this->attributes["pickup_date"])
            return Carbon::parse($this->attributes["pickup_date"])
                ->format("d/m/Y");
        return null;
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function userAddress(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(UserAddress::class);
    }
}

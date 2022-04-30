<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Bin extends Model
{
    protected $guarded = ["id"];

    public function getDecompositionDateAttribute(): ?string
    {
        $date = $this->attributes["decomposition_date"];

        if ($date)
            return Carbon::parse($date)->format("d/m/Y");

        return null;
    }

    public function getBinTypeFormattedAttribute(): string
    {
        $bin_type = $this->attributes["bin_type"];

        return ($bin_type == "drum-bin") ? "200 L Drum" : "240 L Wheelie Bin";
    }

    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function DriverPickups(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DriverPickup::class);
    }
}

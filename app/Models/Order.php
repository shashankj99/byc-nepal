<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = ["id"];

    public function getCreatedAtAttribute(): ?string
    {
        $order_date = $this->attributes["created_at"];

        if ($order_date)
            return Carbon::parse($order_date)
                ->format("d/m/Y");

        return null;
    }

    public function subscription(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getBinTypeAttribute(): ?string
    {
        $bin_type = $this->attributes["bin_type"];

        if ($bin_type) {
            $exploded_bin_type = explode("-", $bin_type);

            if ($exploded_bin_type)
                return ucfirst($exploded_bin_type[0]) . " " . ucfirst($exploded_bin_type[1]);

            return null;
        }

        return null;
    }

    public function bin(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Bin::class);
    }

    public function userAddress(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(UserAddress::class);
    }
}

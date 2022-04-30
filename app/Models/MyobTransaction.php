<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class MyobTransaction extends Model
{
    protected $guarded = ["id"];

    public function getPaymentDateAttribute(): string
    {
        return Carbon::parse($this->attributes["payment_date"])
            ->format("d/m/Y");
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

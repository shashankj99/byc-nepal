<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerBin extends Model
{
    public $timestamps = false;

    protected $guarded = ["id"];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function userAddress(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(UserAddress::class);
    }
}

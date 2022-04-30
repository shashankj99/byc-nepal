<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $guarded = ["id"];

    public function charities(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Charity::class);
    }

    public function customerSubscriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CustomerSubscription::class);
    }
}

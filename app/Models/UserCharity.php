<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCharity extends Model
{
    protected $guarded = ["id"];

    public $timestamps = false;

    public function charity(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Charity::class);
    }
}

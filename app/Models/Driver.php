<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $guarded = ["id"];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

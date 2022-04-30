<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MyobCredential extends Model
{
    protected $guarded = ["id"];

    protected $fillable = ["access_token", "refresh_token", "uid"];
}

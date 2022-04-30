<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BinUser extends Model
{
    protected $table = "bin_user";

    protected $guarded = ["id"];

    public function bin(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Bin::class);
    }
}

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $guarded = ["id"];

    public function getPublishFromFormattedAttribute(): string
    {
        return Carbon::parse($this->attributes["publish_from"])
            ->format("d/m/Y");
    }

    public function getPublishToFormattedAttribute(): string
    {
        return Carbon::parse($this->attributes["publish_to"])
            ->format("d/m/Y");
    }
}

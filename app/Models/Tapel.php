<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Tapel extends Model
{
    public function scopeAktif(Builder $query)
    {
        $query->where('status', 1);
    }
}

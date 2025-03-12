<?php

namespace App\Models;

class Guru extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

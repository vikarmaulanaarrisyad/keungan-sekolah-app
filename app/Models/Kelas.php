<?php

namespace App\Models;

class Kelas extends Model
{
    public function siswa()
    {
        return $this->hasOne(Siswa::class);
    }
}

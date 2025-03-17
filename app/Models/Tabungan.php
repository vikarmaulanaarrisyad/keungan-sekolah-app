<?php

namespace App\Models;

class Tabungan extends Model
{
    // Relasi ke model Siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', 'id');
    }
}

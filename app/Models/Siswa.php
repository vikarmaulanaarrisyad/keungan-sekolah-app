<?php

namespace App\Models;


class Siswa extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function rombel_siswa()
    {
        return $this->belongsToMany(Rombel::class, 'rombel_siswas', 'rombel_id', 'siswa_id')->withTimestamps();
    }

    // Relasi ke model Tabungan
    public function tabungans()
    {
        return $this->hasMany(Tabungan::class);
    }
}

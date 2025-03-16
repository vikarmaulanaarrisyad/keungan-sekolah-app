<?php

namespace App\Models;


class Rombel extends Model
{
    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function tapel()
    {
        return $this->belongsTo(Tapel::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function rombel_siswa()
    {
        return $this->belongsToMany(Siswa::class, 'rombel_siswas', 'rombel_id', 'siswa_id');
    }
}

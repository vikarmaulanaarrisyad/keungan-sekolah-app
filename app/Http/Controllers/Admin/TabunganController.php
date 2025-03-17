<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tabungan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TabunganController extends Controller
{
    public function data()
    {
        $query = Tabungan::with('siswa')->orderBy('id', 'DESC');

        return datatables($query)
            ->addIndexColumn()
            ->addColumn('nama_siswa', function ($tabungan) {
                return $tabungan->siswa ? $tabungan->siswa->nama_siswa : '-';
            })
            ->addColumn('jumlah', function ($tabungan) {
                return format_uang($tabungan->jumlah);
            })
            ->addColumn('saldo_akhir', function ($tabungan) {
                return format_uang($tabungan->saldo_akhir);
            })
            ->addColumn('kelas', function ($tabungan) {
                if ($tabungan->siswa && $tabungan->siswa->kelas && $tabungan->siswa->rombel_siswa) {
                    return $tabungan->siswa->kelas->nama_kelas . ' ' . $tabungan->siswa->rombel_siswa()->first()->nama_rombel;
                }
                return '-';
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function index()
    {
        return view('admin.tabungan.index');
    }
}

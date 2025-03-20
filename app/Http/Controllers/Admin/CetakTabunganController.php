<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\Tabungan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CetakTabunganController extends Controller
{
    public function index()
    {
        $rombels = Rombel::all();
        $siswas = Siswa::all();
        return view('admin.laporan.tabungan.index', compact('rombels', 'siswas'));
    }

    public function data(Request $request)
    {
        $query = Tabungan::with('siswa')
            ->when($request->rombel_id, function ($q) use ($request) {
                return $q->whereHas('siswa.rombel', function ($qr) use ($request) {
                    $qr->where('id', $request->rombel_id);
                });
            })
            ->when($request->bulan, function ($q) use ($request) {
                return $q->whereMonth('tanggal', $request->bulan);
            })
            ->when($request->tahun, function ($q) use ($request) {
                return $q->whereYear('tanggal', $request->tahun);
            })
            ->when($request->siswa_id, function ($q) use ($request) {
                return $q->where('siswa_id', $request->siswa_id);
            })
            ->orderBy('tanggal', 'desc')
            ->get();

        return datatables($query)
            ->addIndexColumn()
            ->addColumn('nama_siswa', function ($tabungan) {
                return $tabungan->siswa ? $tabungan->siswa->nama_siswa : '-';
            })
            ->addColumn('jumlah', function ($tabungan) {
                return format_uang($tabungan->jumlah);
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

    public function cetakPDF(Request $request)
    {
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');
        $siswa_id = $request->siswa_id;

        $query = Tabungan::with('siswa')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->when($siswa_id, function ($q) use ($siswa_id) {
                return $q->where('siswa_id', $siswa_id);
            })
            ->get();

        $pdf = Pdf::loadView('admin.laporan.tabungan.pdf', compact('query', 'bulan', 'tahun'))->setPaper([0, 0, 297, 420]);

        return $pdf->stream('Laporan_Tabungan_' . $bulan . '_' . $tahun . '.pdf');
    }
}

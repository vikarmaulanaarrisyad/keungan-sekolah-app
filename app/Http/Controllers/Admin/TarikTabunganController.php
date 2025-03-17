<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\Tabungan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TarikTabunganController extends Controller
{
    public function index()
    {
        $rombels = Rombel::with('kelas')->get();
        return view('admin.tabungan.tarik.index', compact('rombels'));
    }

    public function data(Request $request)
    {
        $query = Tabungan::with('siswa')
            ->when($request->rombel_id, function ($q) use ($request) {
                return $q->whereHas('siswa', function ($query) use ($request) {
                    $query->whereHas('rombel_siswa', function ($q) use ($request) {
                        $q->where('rombel_id', $request->rombel_id);
                    });
                });
            })
            ->where('type', 'tarik')
            ->orderBy('id', 'DESC')
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
            ->addColumn('aksi', function ($q) {
                $aksi = '';
                if ($q->user_id == Auth::user()->id) {
                    $aksi .= '<button onclick="deleteData(`' . route('tarik-tabungan.destroy', $q->id) . '`, `' . $q->kode . '`)" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i></button>';
                } else {
                    $aksi .= '';
                }

                return $aksi;
            })
            ->escapeColumns([])
            ->make(true);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rombel_id' => 'required',
            'siswa_id' => 'required',
            'jumlah' => 'required|regex:/^[0-9.]+$/'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'errors'  => $validator->errors(),
                'message' => 'Maaf, inputan yang Anda masukkan salah. Silakan periksa kembali dan coba lagi.',
            ], 422);
        }

        DB::beginTransaction(); // Memulai transaksi database

        try {
            // Ambil data siswa
            $siswa = Siswa::findOrFail($request->siswa_id);

            // Generate kode transaksi otomatis
            $kode = 'TARIK-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            $jumlah = str_replace('.', '', $request->jumlah);

            // cek saldo terakhir siswa
            if ($siswa->saldo < $jumlah) {
                return response()->json(['message' => 'Saldo tidak mencukupi'], 400);
            }

            // Hitung saldo akhir
            $saldoAkhir = $siswa->saldo - $jumlah;

            // Simpan transaksi tabungan
            $tabungan = Tabungan::create([
                'user_id' => Auth::user()->id,
                'siswa_id' => $request->siswa_id,
                'kode' => $kode,
                'tanggal' => now(),
                'type' => $request->type,
                'jumlah' => $jumlah,
                'saldo_akhir' => $saldoAkhir,
            ]);

            // Update saldo siswa
            $siswa->update(['saldo' => $saldoAkhir]);

            DB::commit(); // Simpan perubahan jika semua sukses

            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi berhasil disimpan dan saldo siswa diperbarui.',
                'data' => $tabungan
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan perubahan jika ada error

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan transaksi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $tabungan = Tabungan::find($id); // Gunakan find() untuk lebih ringkas

        if (!$tabungan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $siswa = $tabungan->siswa;

        if ($siswa) {
            $siswa->saldo += $tabungan->jumlah;
            $siswa->save();
        }

        // Hapus tabungan
        $tabungan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil dihapus'
        ], 200);
    }
}

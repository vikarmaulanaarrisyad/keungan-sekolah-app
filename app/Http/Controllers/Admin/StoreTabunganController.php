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

class StoreTabunganController extends Controller
{
    public function index()
    {
        $rombels = Rombel::with('kelas')->get();
        return view('admin.tabungan.store.index', compact('rombels'));
    }

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
            ->addColumn('kelas', function ($tabungan) {
                if ($tabungan->siswa && $tabungan->siswa->kelas && $tabungan->siswa->rombel_siswa) {
                    return $tabungan->siswa->kelas->nama_kelas . ' ' . $tabungan->siswa->rombel_siswa()->first()->nama_rombel;
                }
                return '-';
            })
            ->addColumn('aksi', function ($q) {
                $aksi = '';
                if ($q->user_id == Auth::user()->id) {
                    $aksi .= '<button onclick="deleteData(`' . route('store-tabungan.destroy', $q->id) . '`, `' . $q->kode . '`)" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i></button>';
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
            $kode = 'SETOR-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            $jumlah = str_replace('.', '', $request->jumlah);

            // Hitung saldo akhir
            $saldoAkhir = $siswa->saldo + $jumlah;

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
            $siswa->saldo -= $tabungan->jumlah;
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

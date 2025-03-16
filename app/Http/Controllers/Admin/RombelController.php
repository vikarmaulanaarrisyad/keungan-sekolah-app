<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\Tapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RombelController extends Controller
{
    public function index()
    {
        return view('admin.rombel.index');
    }

    public function data()
    {
        $tapel = Tapel::aktif()->first();
        $data = Rombel::where('tapel_id', $tapel->id)->get();

        return datatables($data)
            ->addIndexColumn()
            ->editColumn('kelas', function ($q) {
                return $q->kelas->nama_kelas;
            })
            ->editColumn('wali_kelas', function ($q) {
                return $q->guru->nama_guru ?? '';
            })
            ->editColumn('jumlah_siswa', function ($q) {
                return '';
            })
            ->addColumn('aksi', function ($q) {
                return '
                    <button onclick="detail(\'' . route('rombel.detail', $q->id) . '\')" class="btn btn-sm btn-primary">DETAIL</button>
                ';
            })

            ->escapeColumns([])
            ->make(true);
    }

    public function create()
    {
        $guru = Guru::all();
        $kelas = Kelas::all();

        return view('admin.rombel.create', compact('guru', 'kelas'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guru_id' => 'required',
            'kelas_id' => 'required',
            'nama_rombel' => 'required',
        ]);

        $tapel = Tapel::aktif()->first();

        Rombel::create([
            'tapel_id' => $tapel->id,
            'kelas_id' => $request->kelas_id,
            'nama_rombel' => $request->nama_rombel,
            'guru_id' => $request->guru_id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'errors'  => $validator->errors(),
                'message' => 'Maaf, inputan yang Anda masukkan salah. Silakan periksa kembali dan coba lagi.',
            ], 422);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Data berhasil diperbarui',
        ], 200);
    }

    public function detail($id)
    {
        $rombel = Rombel::with('tapel')->findOrfail($id);

        return view('admin.rombel.detail', compact('rombel'));
    }

    public function edit($id)
    {
        $rombel = Rombel::findOrfail($id);
        $kelas = Kelas::all();
        $guru = Guru::all();

        return view('admin.rombel.edit', compact('rombel', 'kelas', 'guru'));
    }

    public function update(Request $request, $id)
    {
        // Aturan validasi untuk setiap field
        $rules = [
            'nama_rombel' => 'required',  // Validasi dengan pengecekan di tabel guru untuk wali kelas
            'guru_id' => 'required',  // Validasi dengan pengecekan di tabel guru untuk wali kelas
        ];

        // Pesan kesalahan kustom
        $messages = [
            'guru_id.required' => 'Wali Kelas harus dipilih.',
        ];

        // Validasi input berdasarkan rules dan pesan kesalahan
        $validator = Validator::make($request->all(), $rules, $messages);

        // Jika validasi gagal, kembalikan respons dengan status 422 dan pesan kesalahan
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
                'message' => 'Maaf, inputan yang Anda masukkan salah. Silakan periksa kembali dan coba lagi.',
            ], 422);
        }

        // Menyimpan data rombel ke dalam database
        $data = [
            'guru_id' => $request->guru_id,
            'nama_rombel' => $request->nama_rombel,
        ];

        $rombel = Rombel::findOrfail($id);
        $rombel->update($data);

        // Jika data berhasil disimpan, kirimkan response sukses
        return response()->json([
            'status' => 'success',
            'message' => 'Data Rombel berhasil disimpan!',
            'data' => $data,
        ], 201);
    }

    // mendapatkan data siswa
    public function getDataSiswa(Request $request)
    {
        // Dapatkan rombel dari request
        $rombel = Rombel::find($request->rombel_id);

        if (!$rombel) {
            return response()->json(['success' => false, 'message' => 'Rombel tidak ditemukan.']);
        }

        // Cek apakah tahun pelajaran aktif tersedia
        $tahunPelajaran = Tapel::aktif()->first();
        if (!$tahunPelajaran) {
            return response()->json(['success' => false, 'message' => 'Tahun pelajaran aktif tidak ditemukan.']);
        }

        $kelasId = $rombel->kelas->id;

        // Ambil siswa yang sudah terdaftar di sembarang rombel pada tahun pelajaran aktif
        $siswaTerdaftarIds = DB::table('rombel_siswas')
            ->pluck('siswa_id')
            ->toArray();

        // Ambil siswa yang belum terdaftar di rombel mana pun pada tahun pelajaran aktif
        $siswa = Siswa::where('kelas_id', $kelasId)
            ->whereNotIn('id', $siswaTerdaftarIds) // Saring siswa yang sudah terdaftar
            ->get();

        // Kembalikan data siswa dalam format DataTables
        return datatables($siswa)
            ->addIndexColumn()
            ->addColumn('aksi', function ($siswa) {
                return '<input type="checkbox" class="select-siswa" name="siswa_id[]" value="' . $siswa->id . '">';
            })
            ->escapeColumns([])
            ->make(true);
    }


    public function getSiswaRombel($id)
    {
        $siswa = Siswa::whereHas('rombel_siswa', function ($query) use ($id) {
            $query->where('rombel_id', $id);
        })
            ->orderBy('nama_siswa', 'asc')
            ->get();

        return datatables($siswa)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                return '
                    <button type="button" onclick="hapusSiswa(' . $row->id . ')" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> Hapus</button>
                ';
            })
            ->skipPaging()
            ->rawColumns(['aksi']) // Pastikan HTML tidak di-escape
            ->make(true);
    }

    public function addSiswa(Request $request)
    {
        // Validasi request
        $validated = $request->validate([
            'rombel_id' => 'required|exists:rombels,id',
            'siswa_ids' => 'required|array',
        ]);

        // Dapatkan tahun pelajaran aktif
        $tahunPelajaran = Tapel::aktif()->first();
        if (!$tahunPelajaran) {
            return response()->json(['success' => false, 'message' => 'No active academic year found.']);
        }

        // Temukan rombel yang dipilih
        $rombel = Rombel::findOrFail($validated['rombel_id']);

        // Ambil siswa yang belum terdaftar dalam rombel ini
        $existingSiswaIds = DB::table('rombel_siswas')
            ->where('rombel_id', $rombel->id)
            ->whereIn('siswa_id', $validated['siswa_ids'])
            ->pluck('siswa_id')
            ->toArray();

        // Filter siswa yang belum ada
        $newSiswaIds = array_diff($validated['siswa_ids'], $existingSiswaIds);

        if (!empty($newSiswaIds)) {
            // Tambahkan siswa ke rombel (gunakan syncWithoutDetaching agar tidak duplikat)
            $rombel->rombel_siswa()->syncWithoutDetaching($newSiswaIds);

            // Update kelas_id untuk siswa
            Siswa::whereIn('id', $newSiswaIds)->update(['kelas_id' => $rombel->kelas->id]);

            // Ambil data siswa yang baru ditambahkan
            $siswa = Siswa::whereIn('id', $newSiswaIds)->get();

            return response()->json([
                'success' => true,
                'siswa' => $siswa,
                'message' => 'Siswa berhasil ditambahkan ke rombel.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Tidak ada siswa yang ditambahkan karena sudah terdaftar.'
        ]);
    }

    public function removeSiswa(Request $request)
    {
        try {
            $siswaId = $request->input('siswa_id');
            $rombelId = $request->input('rombel_id');

            // Cari siswa
            $siswa = Siswa::find($siswaId);

            if (!$siswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa tidak ditemukan.'
                ], 404);
            }

            // Hapus relasi siswa dari rombel
            $deleted = $siswa->rombel_siswa()->detach($rombelId);

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Siswa berhasil dihapus dari rombel.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa tidak terdaftar dalam rombel ini.'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus siswa.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

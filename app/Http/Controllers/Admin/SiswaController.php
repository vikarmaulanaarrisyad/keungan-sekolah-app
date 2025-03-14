<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SiswaController extends Controller
{
    public function data()
    {
        $data = Siswa::all();
        return datatables($data)
            ->addIndexColumn()
            ->addColumn('foto', function ($q) {
                if ($q->foto) {
                    $foto = Storage::url($q->foto);
                } else {
                    // Menentukan foto default berdasarkan jenis kelamin
                    if ($q->jenis_kelamin == 'P') {
                        $foto = asset('AdminLTE/dist/img/avatar3.png'); // Ganti dengan gambar perempuan yang sesuai
                    } else {
                        $foto = asset('AdminLTE/dist/img/avatar4.png'); // Gambar default laki-laki
                    }
                }

                return '<img src="' . $foto . '" class="rounded-circle" width="50px" height="50px">';
            })
            ->editColumn('ttl', function ($q) {
                return $q->tempat_lahir . ', ' . tanggal_indonesia($q->tanggal_lahir);
            })

            ->editColumn('rombel', function ($q) {
                return '0';
            })
            ->addColumn('aksi', function ($q) {
                return '
                <button onclick="editForm(`' . route('siswa.edit', $q->id) . '`)" class="btn btn-sm btn-success" title="Edit"><i class="fas fa-pencil-alt"></i></button>
                <button onclick="deleteData(`' . route('siswa.destroy', $q->id) . '`, `' . $q->nama_siswa . '`)" class="btn btn-sm btn-danger" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                ';
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function index()
    {
        return view('admin.siswa.index');
    }

    public function create()
    {
        $kelas = Kelas::orderBy('nama_kelas', 'ASC')->get();

        return view('admin.siswa.create', compact('kelas'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_siswa' => 'required',
            'nisn_siswa' => 'required|min:10',
            'nis_siswa' => 'required',
            'tanggal_lahir' => 'required|date',
            'anakke' => 'required|integer',
            'jumlah_saudara' => 'required|integer',
            'berat_badan' => 'required|numeric',
            'tinggi_badan' => 'required|numeric',
            'rt' => 'required|integer',
            'rw' => 'required|integer',
            'kode_pos' => 'required|digits:5',
            'dusun' => 'required',
            'kelurahan' => 'required',
            'alamat_siswa' => 'required',
            'jenis_tinggal' => 'required',
            'transportasi' => 'required',
            'foto' => 'nullable|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'errors'  => $validator->errors(),
                'message' => 'Maaf, inputan yang Anda masukkan salah. Silakan periksa kembali dan coba lagi.',
            ], 422);
        }

        DB::beginTransaction(); // Mulai transaksi

        try {
            // Generate email otomatis berdasarkan NISN
            $email = strtolower($request->nisn_siswa) . '@gmail.com';
            $username = strtolower($request->nama_siswa);

            // Buat atau update User berdasarkan email
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $request->nama_siswa,
                    'username' => $username,
                    'password' => Hash::make($request->nisn_siswa ?? 'password')
                ]
            );

            if (!$user->hasRole('siswa')) {
                $user->assignRole('siswa');
            }

            $data = $request->except('foto');

            $data['foto'] = upload('siswa', $request->file('foto'), 'siswa');
            $data['user_id'] = $user->id;
            // $data = [
            //     'user_id' => $user->id,
            //     'nama_siswa' => $request->nama_siswa,
            //     'nisn_siswa' => $request->nisn_siswa,
            //     'nis_siswa' => $request->nis_siswa,
            //     'tempat_lahir' => $request->tempat_lahir,
            //     'tanggal_lahir' => $request->tanggal_lahir,
            //     'jenis_kelamin' => $request->jenis_kelamin,
            //     'anakke' => $request->anakke,
            //     'jumlah_saudara' => $request->jumlah_saudara,
            //     'agama' => $request->agama,
            //     'nomor_hp' => $request->nomor_hp,
            //     'berat_badan' => $request->berat_badan,
            //     'tinggi_badan' => $request->tinggi_badan,
            //     'lingkar_kepala' => $request->lingkar_kepala,
            //     'kelas_id' => $request->kelas_id,
            //     'foto' => upload('siswa', $request->file('foto'), $request->nisn_siswa)
            // ];
            // Simpan data ke database
            Siswa::create($data);

            DB::commit(); // Simpan transaksi jika berhasil

            return response()->json([
                'status'  => 'success',
                'message' => 'Data siswa berhasil disimpan',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan transaksi jika ada error

            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data siswa.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        $siswa = Siswa::with('kelas')->findOrFail($id);
        $kelas = Kelas::all(); // Ambil semua data kelas

        return view('admin.siswa.edit', compact('siswa', 'kelas'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_siswa' => 'required',
            'nisn_siswa' => 'required|min:10',
            'nis_siswa' => 'required',
            'tanggal_lahir' => 'required|date',
            'anakke' => 'required|integer',
            'jumlah_saudara' => 'required|integer',
            'berat_badan' => 'required|numeric',
            'tinggi_badan' => 'required|numeric',
            'rt' => 'required|integer',
            'rw' => 'required|integer',
            'kode_pos' => 'required|digits:5',
            'dusun' => 'required',
            'kelurahan' => 'required',
            'alamat_siswa' => 'required',
            'jenis_tinggal' => 'required',
            'transportasi' => 'required',
            'foto' => 'nullable|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'errors'  => $validator->errors(),
                'message' => 'Maaf, inputan yang Anda masukkan salah. Silakan periksa kembali dan coba lagi.',
            ], 422);
        }

        DB::beginTransaction(); // Mulai transaksi

        try {
            // Cari data siswa berdasarkan ID
            $siswa = Siswa::findOrFail($id);

            $data = $request->except('foto');

            // Generate email otomatis berdasarkan NISN
            $email = strtolower($request->nisn_siswa) . '@gmail.com';
            $username = strtolower($request->nama_siswa);

            // Update atau buat User berdasarkan email
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $request->nama_siswa,
                    'username' => $username,
                    'password' => Hash::make($request->nisn_siswa ?? 'password')
                ]
            );

            if (!$user->hasRole('siswa')) {
                $user->assignRole('siswa');
            }

            // Simpan file jika ada perubahan foto
            if ($request->hasFile('foto')) {
                if (Storage::disk('public')->exists($siswa->foto)) {
                    Storage::disk('public')->delete($siswa->foto);
                }

                $file = $request->file('foto');
                $data['foto'] = upload('siswa', $file, 'siswa');
            }

            $data['user_id'] = $user->id;

            // **Perbarui data siswa yang sudah ada, bukan menambah baru**
            $siswa->update($data);

            DB::commit(); // Simpan transaksi jika berhasil

            return response()->json([
                'status'  => 'success',
                'message' => 'Data siswa berhasil diperbarui',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan transaksi jika ada error

            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui data siswa.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        $siswa = Siswa::findOrfail($id);

        if (Storage::disk('public')->exists($siswa->foto)) {
            Storage::disk('public')->delete($siswa->foto);
        }

        $siswa->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil dihapus'
        ], 201);
    }
}

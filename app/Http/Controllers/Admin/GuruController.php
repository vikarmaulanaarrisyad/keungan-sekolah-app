<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class GuruController extends Controller
{
    public function data()
    {
        $data = Guru::orderBy('nama_guru', 'ASC')->get();

        return datatables($data)
            ->addIndexColumn()
            ->addColumn('foto', function ($q) {
                if ($q->foto) {
                    $foto = Storage::url($q->foto);
                } else {
                    // Menentukan foto default berdasarkan jenis kelamin
                    if ($q->jenis_kelamin->nama == 'P') {
                        $foto = asset('AdminLTE/dist/img/avatar3.png'); // Ganti dengan gambar perempuan yang sesuai
                    } else {
                        $foto = asset('AdminLTE/dist/img/avatar4.png'); // Gambar default laki-laki
                    }
                }

                return '<img src="' . $foto . '" class="rounded-circle" width="50px" height="50px">';
            })
            ->editColumn('nama_guru', function ($q) {
                return trim(
                    ($q->gelar_depan ? $q->gelar_depan . ' ' : '') .
                        $q->nama_guru .
                        ($q->gelar_belakang ? ', ' . $q->gelar_belakang : '')
                );
            })
            ->addColumn('ttl', function ($q) {
                return $q->tempat_lahir . ', ' . tanggal_indonesia($q->tanggal_lahir);
            })
            ->addColumn('aksi', function ($q) {
                return '
                <button onclick="editForm(`' . route('guru.show', $q->id) . '`)" class="btn btn-sm btn-success" title="Edit"><i class="fas fa-pencil-alt"></i></button>
                <button onclick="deleteData(`' . route('guru.destroy', $q->id) . '`, `' . $q->nama_guru . '`)" class="btn btn-sm btn-danger" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                ';
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function index()
    {
        return view('admin.guru.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'username' => 'required',
            'nama_guru' => 'required',
            'nip_guru' => 'required',
            'gelar_depan' => 'nullable',
            'gelar_belakang' => 'nullable',
            'jenis_kelamin' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required|date',
            'foto' => 'required|mimes:jpg,png,jpeg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'errors'  => $validator->errors(),
                'message' => 'Maaf, inputan yang Anda masukkan salah. Silakan periksa kembali dan coba lagi.',
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Buat atau update User berdasarkan email
            $user = User::updateOrCreate(
                ['email' => $request->email],
                [
                    'name' => $request->nama_guru,
                    'username' => $request->username,
                    'password' => Hash::make($request->password ?? 'password')
                ]
            );

            // Beri role "guru" hanya jika belum memiliki role tersebut
            if (!$user->hasRole('guru')) {
                $user->assignRole('guru');
            }

            // Simpan data guru
            $data = [
                'user_id'           => $user->id,
                'nama_guru'         => $request->nama_guru,
                'tempat_lahir'      => $request->tempat_lahir,
                'tanggal_lahir'     => $request->tanggal_lahir,
                'gelar_depan'       => $request->gelar_depan,
                'gelar_belakang'    => $request->gelar_belakang,
                'nip_guru'          => $request->nip_guru,
                'jenis_kelamin'     => $request->jenis_kelamin,
                'foto'              => upload('guru', $request->file('foto'), 'guru')
            ];

            Guru::create($data);

            DB::commit(); // Simpan perubahan

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil disimpan',
                'user' => $user
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack(); // Batalkan perubahan jika terjadi error

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data user',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $data = Guru::with('user')->findOrfail($id);
        $data['email'] = $data->user->email;
        $data['username'] = $data->user->username;
        return response()->json(['data' => $data]);
    }

    public function update(Request $request, $id)
    {
        // Cari Guru berdasarkan ID
        $guru = Guru::find($id);

        if (!$guru) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data Guru tidak ditemukan.',
            ], 404);
        }

        // Temukan User berdasarkan user_id dari Guru
        $user = User::findOrFail($guru->user_id);

        // Validasi input
        $validator = Validator::make($request->all(), [
            'email'           => 'required|email|unique:users,email,' . $user->id,
            'nama_guru'       => 'required',
            'nip_guru'        => 'nullable',
            'gelar_depan'     => 'nullable',
            'gelar_belakang'  => 'nullable',
            'tempat_lahir'    => 'required',
            'tanggal_lahir'   => 'required|date',
            'foto'            => 'nullable|mimes:jpg,png,jpeg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'errors'  => $validator->errors(),
                'message' => 'Maaf, inputan yang Anda masukkan salah. Silakan periksa kembali dan coba lagi.',
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Update data User
            $user->update([
                'name'     => $request->nama_guru,
                'username' => $request->username,
                'email'    => $request->email,
                'password' => $request->filled('password') ? Hash::make($request->password) : $user->password,
            ]);

            // Jika ada foto baru yang diunggah
            if ($request->hasFile('foto')) {
                // Hapus foto lama jika ada
                if (Storage::disk('public')->exists($guru->foto)) {
                    Storage::disk('public')->delete($guru->foto);
                }

                // Simpan foto baru
                $fotoPath = upload('guru', $request->file('foto'), 'guru');
            } else {
                $fotoPath = $guru->foto; // Gunakan foto lama jika tidak ada perubahan
            }

            // Update data Guru tanpa menggunakan updateOrCreate
            $guru->update([
                'nama_guru'      => $request->nama_guru,
                'tempat_lahir'   => $request->tempat_lahir,
                'tanggal_lahir'  => $request->tanggal_lahir,
                'gelar_depan'    => $request->filled('gelar_depan') ? $request->gelar_depan : null,
                'gelar_belakang' => $request->filled('gelar_belakang') ? $request->gelar_belakang : null,
                'nip_guru'       => $request->filled('nip_guru') ? $request->nip_guru : null,
                'jenis_kelamin'  => $request->jenis_kelamin,
                'foto'           => $fotoPath,
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data berhasil diperbarui',
                'user'    => $user
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui data user',
                'error'   => $th->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $guru = Guru::findOrfail($id);

        if (Storage::disk('public')->exists($guru->foto)) {
            Storage::disk('public')->delete($guru->foto);
        }

        $guru->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil dihapus'
        ], 201);
    }
}

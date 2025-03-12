<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TapelController extends Controller
{
    public function data()
    {
        $data = Tapel::orderBy('id', 'DESC')->get();

        return datatables($data)
            ->addIndexColumn()
            ->editColumn('status', function ($q) {
                $icon = $q->status ? 'fa-toggle-on text-success' : 'fa-toggle-off text-danger';
                return '
                <button onclick="updateStatus(' . $q->id . ')" class="status-toggle btn-link" kodeq="' . $q->id . '">
                    <i class="fas ' . $icon . ' fa-lg"></i>
                </button>
            ';
            })
            ->addColumn('aksi', function ($q) {
                return '
                <button onclick="editForm(`' . route('tapel.show', $q->id) . '`)" class="btn btn-sm btn-success" title="Edit"><i class="fas fa-pencil-alt"></i></button>
            ';
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function index()
    {
        return view('admin.tapel.index');
    }

    public function show($id)
    {
        $data = Tapel::findOrfail($id);

        return response()->json(['data' => $data]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|min:1',
            'semester' => 'required|in:Ganjil,Genap'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'errors'  => $validator->errors(),
                'message' => 'Maaf, inputan yang Anda masukkan salah. Silakan periksa kembali dan coba lagi.',
            ], 422);
        }

        $tapel = Tapel::findOrfail($id);
        $tapel->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil disimpan'
        ], 201);
    }

    public function updateStatus($id)
    {
        $tapel = Tapel::findOrfail($id);

        if ($tapel->status == 0) {
            Tapel::where('status', 1)->update(['status' => 0]);
            $tapel->status = 1;
        } else {
            // cegah menonaktifkan jika hanya satu yang aktif
            if (Tapel::where('status', 1)->count() <= 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Minimal satu Tahun Pelajaran harus tetap aktif!'
                ], 400);
            }

            $tapel->status = 0;
        }

        $tapel->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Status berhasil diperbarui!',
            'new_status' => $tapel->status
        ]);
    }
}

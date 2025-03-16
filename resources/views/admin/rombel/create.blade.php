@extends('layouts.app')

@section('title', 'Tambah Rombongan Belajar')

@section('subtitle', 'Tambah Rombongan Belajar')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">@yield('title')</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <form id="formRombel" action="{{ route('rombel.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <x-card>
                    <x-slot name="header">
                        <h3 class="card-title">@yield('title')</h3>
                    </x-slot>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="kelas_id">Kelas</label>
                                <select name="kelas_id" id="kelas_id"
                                    class="form-control @error('kelas_id') is-invalid @enderror">
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach ($kelas as $kls)
                                        <option value="{{ $kls->id }}"
                                            {{ old('kelas_id') == $kls->id ? 'selected' : '' }}>{{ $kls->nama_kelas }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kelas_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nama_rombel">Nama Rombel</label>
                                <input type="text" name="nama_rombel" id="nama_rombel"
                                    class="form-control @error('nama_rombel') is-invalid @enderror"
                                    value="{{ old('nama_rombel') }}">
                                @error('nama_rombel')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="guru_id">Wali Kelas</label>
                                <select name="guru_id" id="guru_id"
                                    class="form-control @error('guru_id') is-invalid @enderror">
                                    <option value="">-- Pilih Wali Kelas --</option>
                                    @foreach ($guru as $item)
                                        <option value="{{ $item->id }}"
                                            {{ old('guru_id') == $item->id ? 'selected' : '' }}>{{ $item->nama_guru }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('guru_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <x-slot name="footer">
                        <button type="button" class="btn btn-warning btn-sm" onclick="kembali()">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </button>
                        <button type="submit" class="btn btn-primary float-right">Simpan</button>
                    </x-slot>
                </x-card>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function kembali() {
            window.location.href = '{{ route('rombel.index') }}'
        }

        $(document).ready(function() {
            // Hapus error saat input berubah
            $('input, select').on('input change', function() {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            });

            $('#formRombel').on('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Mohon Tunggu',
                    text: 'Sedang menyimpan data...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                let formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data siswa berhasil disimpan',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        let response = xhr.responseJSON;
                        let errorMessage = 'Terjadi kesalahan saat menyimpan data';

                        if (response && response.errors) {
                            errorMessage = '<ul>';
                            $.each(response.errors, function(key, value) {
                                errorMessage += '<li>' + value[0] + '</li>';
                                let inputField = $('#' + key);

                                inputField.addClass('is-invalid');

                                if (inputField.next('.invalid-feedback').length === 0) {
                                    inputField.after('<div class="invalid-feedback">' +
                                        value[0] + '</div>');
                                }
                            });
                            errorMessage += '</ul>';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat menyimpan data',
                        });
                    }
                });
            });
        });
    </script>
@endpush

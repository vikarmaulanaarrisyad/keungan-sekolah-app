@extends('layouts.app')

@section('title', 'Data Siswa')

@section('subtitle', 'Data Siswa')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">@yield('title')</li>
@endsection


@section('content')
    <x-card title="Tambah Data Siswa">
        <form id="formSiswa" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <x-slot name="header">
                <h3 class="card-title">Data Siswa</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-warning btn-sm" onclick="kembali()">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </button>
                </div>
            </x-slot>

            <div class="row">
                <div class="col-lg-12">
                    <div class="alert alert-success alert-dismissible">
                        A. IDENTITAS PESERTA DIDIK
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nama_siswa">Nama Siswa</label>
                        <input type="text" class="form-control" name="nama_siswa" id="nama_siswa" autocomplete="off"
                            value="{{ $siswa->nama_siswa }}">
                    </div>
                    <div class="form-group">
                        <label for="nisn_siswa">NISN</label>
                        <input type="number" class="form-control" name="nisn_siswa" id="nisn_siswa" autocomplete="off"
                            value="{{ $siswa->nisn_siswa }}">
                    </div>
                    <div class="form-group">
                        <label for="nis_siswa">NIPD</label>
                        <input type="number" class="form-control" name="nis_siswa" id="nis_siswa" autocomplete="off"
                            value="{{ $siswa->nis_siswa }}">
                    </div>
                    <div class="form-group">
                        <label for="tempat_lahir">Tempat Lahir</label>
                        <input type="text" class="form-control" name="tempat_lahir" id="tempat_lahir" autocomplete="off"
                            value="{{ $siswa->tempat_lahir }}">
                    </div>
                    <div class="form-group">
                        <label for="tanggal_lahir">Tanggal Lahir</label>
                        <input type="date" class="form-control" name="tanggal_lahir" id="tanggal_lahir"
                            autocomplete="off" value="{{ $siswa->tanggal_lahir }}">
                    </div>
                    <div class="form-group">
                        <label for="jenis_kelamin">Jenis Kelamin</label>
                        <select class="form-control" name="jenis_kelamin" id="jenis_kelamin">
                            <option disabled selected>Pilih salah satu</option>
                            <option value="L" {{ $siswa->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-Laki</option>
                            <option value="P" {{ $siswa->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="anakke">Anak Ke</label>
                        <input type="number" class="form-control" name="anakke" id="anakke"
                            value="{{ $siswa->anakke }}">
                    </div>
                    <div class="form-group">
                        <label for="jumlah_saudara">Jumlah Saudara</label>
                        <input type="number" class="form-control" name="jumlah_saudara" id="jumlah_saudara"
                            value="{{ $siswa->jumlah_saudara }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="agama">Agama</label>
                        <select class="form-control" name="agama" id="agama">
                            <option disabled {{ old('agama', $siswa->agama ?? '') == '' ? 'selected' : '' }}>Pilih salah
                                satu
                            </option>
                            <option value="1" {{ old('agama', $siswa->agama ?? '') == '1' ? 'selected' : '' }}>Islam
                            </option>
                            <option value="2" {{ old('agama', $siswa->agama ?? '') == '2' ? 'selected' : '' }}>Kristen
                            </option>
                            <option value="3" {{ old('agama', $siswa->agama ?? '') == '3' ? 'selected' : '' }}>Katolik
                            </option>
                            <option value="4" {{ old('agama', $siswa->agama ?? '') == '4' ? 'selected' : '' }}>Hindu
                            </option>
                            <option value="5" {{ old('agama', $siswa->agama ?? '') == '5' ? 'selected' : '' }}>Buddha
                            </option>
                        </select>
                    </div>


                    <div class="form-group">
                        <label for="nomor_hp">Nomor HP</label>
                        <input type="text" class="form-control" name="nomor_hp" id="nomor_hp"
                            value="{{ $siswa->nomor_hp }}">
                    </div>
                    <div class="form-group">
                        <label for="berat_badan">Berat Badan (Kg)</label>
                        <input type="number" class="form-control" name="berat_badan" id="berat_badan"
                            value="{{ $siswa->berat_badan }}">
                    </div>
                    <div class="form-group">
                        <label for="tinggi_badan">Tinggi Badan (cm)</label>
                        <input type="number" class="form-control" name="tinggi_badan" id="tinggi_badan"
                            value="{{ $siswa->tinggi_badan }}">
                    </div>
                    <div class="form-group">
                        <label for="lingkar_kepala">Lingkat Kepala</label>
                        <input type="number" class="form-control" name="lingkar_kepala" id="lingkar_kepala"
                            value="{{ $siswa->lingkar_kepala }}">
                    </div>
                    <div class="form-group">
                        <label for="kelas_id">Rombel/Kelas</label>
                        <select name="kelas_id" id="kelas_id" class="form-control">
                            <option disabled selected>Pilih salah satu</option>
                            @foreach ($kelas as $k)
                                <option value="{{ $k->id }}" {{ $siswa->kelas_id == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="foto">Unggah Foto Siswa</label>
                        <input type="file" id="foto" class="form-control" name="foto" accept="image/*">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="alert alert-success alert-dismissible">
                        B. ALAMAT SISWA
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-2">
                    <div class="form-group">
                        <label for="rt">RT</label>
                        <input type="number" class="form-control" name="rt" id="rt"
                            value="{{ $siswa->rt }}">
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <label for="rw">RW</label>
                        <input type="number" class="form-control" name="rw" id="rw"
                            value="{{ $siswa->rw }}">
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <label for="kode_pos">Kode Pos</label>
                        <input type="number" class="form-control" name="kode_pos" id="kode_pos"
                            value="{{ $siswa->kode_pos }}">
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <label for="dusun">Dusun</label>
                        <input type="text" class="form-control" name="dusun" id="dusun"
                            value="{{ $siswa->dusun }}">
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <label for="kelurahan">Kelurahan</label>
                        <input type="text" class="form-control" name="kelurahan" id="kelurahan"
                            value="{{ $siswa->kelurahan }}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="alamat_siswa">Alamat</label>
                        <textarea class="form-control" name="alamat_siswa" id="alamat_siswa">{{ $siswa->alamat_siswa }}</textarea>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <label for="jenis_tinggal">Jenis Tinggal</label>
                        <input type="text" class="form-control" name="jenis_tinggal" id="jenis_tinggal"
                            value="{{ $siswa->jenis_tinggal }}">
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <label for="transportasi">Transportasi</label>
                        <input type="text" class="form-control" name="transportasi" id="transportasi"
                            value="{{ $siswa->transportasi }}">
                    </div>
                </div>

            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('siswa.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </x-card>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#formSiswa').submit(function(e) {
                e.preventDefault(); // Mencegah reload halaman

                let form = $(this);
                let submitButton = form.find('button[type="submit"]');

                // Ubah tampilan tombol simpan
                submitButton.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

                // Tampilkan Swal loading
                Swal.fire({
                    title: 'Menyimpan Data...',
                    text: 'Harap tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Kirim data dengan AJAX
                $.ajax({
                    url: "{{ route('siswa.update', $siswa->id) }}", // Sesuaikan dengan route penyimpanan
                    type: "POST",
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data siswa berhasil disimpan.',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href =
                                "{{ route('siswa.index') }}"; // Redirect ke daftar siswa
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan, periksa kembali inputan Anda.',
                        });

                        // Kembalikan tombol simpan ke keadaan semula
                        submitButton.prop('disabled', false).html('Simpan');
                    }
                });
            });
        });
    </script>
@endpush

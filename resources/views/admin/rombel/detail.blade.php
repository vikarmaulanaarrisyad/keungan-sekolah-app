@extends('layouts.app')

@section('title', 'Detail Rombongan Belajar')
@section('subtitle', 'Detail Rombongan Belajar')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('rombel.index') }}">Rombongan Belajar</a></li>
    <li class="breadcrumb-item active">Detail Rombongan Belajar</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <x-card>
                <x-slot name="header">
                    <h6 class="card-title"><i class="fas fa-users mr-1 mt-2"></i>@yield('subtitle')</h6>
                    <div class="card-tools">
                        <div class="d-flex align-items-center">
                            <div>
                                <button onclick="edit(`{{ route('rombel.edit', $rombel->id) }}`)"
                                    class="btn btn-sm btn-primary">
                                    <i class="fas fa-pencil-alt"></i> Edit Rombel
                                </button>
                            </div>
                        </div>
                    </div>
                </x-slot>

                <div class="row">
                    <div class="col-12 col-sm-3">
                        <div class="info-box bg-light">
                            <div class="info-box-content">
                                <span class="info-box-text text-center text-muted">Tahun Pelajaran</span>
                                <span class="info-box-number text-center text-muted mb-0">
                                    {{ $rombel->tapel->nama }}
                                    {{ $rombel->tapel->semester }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="info-box bg-light">
                            <div class="info-box-content">
                                <span class="info-box-text text-center text-muted">Wali Kelas</span>
                                <span class="info-box-number text-center text-muted mb-0">
                                    {{ $rombel->guru->nama_lengkap ?? 'Belum ada' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="info-box bg-light">
                            <div class="info-box-content">
                                <span class="info-box-text text-center text-muted">Tingkat Kelas</span>
                                <span class="info-box-number text-center text-muted mb-0">
                                    {{ $rombel->kelas->nama_kelas }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="info-box bg-light">
                            <div class="info-box-content">
                                <span class="info-box-text text-center text-muted">Nama Rombel</span>
                                <span class="info-box-number text-center text-muted mb-0">
                                    {{ $rombel->nama_rombel }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="badge badge-info" style="font-size: 15px !important;">Jumlah Siswa: <strong
                            id="jumlahSiswa">{{ $rombel->rombel_siswa->count() ?? 0 }}</strong></span>
                    <button onclick="modalTambahSiswa()" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus-circle"></i> Tambah Siswa
                    </button>
                </div>

                <x-table class="rombel-siswa">
                    <x-slot name="thead">
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>NISN</th>
                        <th>NIPD</th>
                        <th></th>
                    </x-slot>
                </x-table>
            </x-card>
        </div>
    </div>
    @include('admin.rombel.select_siswa')
@endsection

@include('includes.datatables')

@push('scripts')
    <script>
        let table1, table2;
        let modal = '#modalTambahSiswa';

        table1 = $('.rombel-siswa').DataTable({
            serverSide: true,
            autoWidth: false,
            responsive: true,
            paging: false, // Nonaktifkan pagination agar semua data ditampilkan
            language: {
                "processing": "Mohon bersabar..."
            },
            ajax: {
                url: '{{ route('rombel.getSiswaRombel', $rombel->id) }}',
            },
            columns: [{
                    data: 'DT_RowIndex',
                    searchable: false,
                    sortable: false
                },
                {
                    data: 'nama_siswa'
                },
                {
                    data: 'nisn_siswa'
                },
                {
                    data: 'nis_siswa'
                },
                {
                    data: 'aksi',
                    sortable: false,
                    searchable: false
                },
            ],
            dom: 'Brt',
            bSort: false,
        });

        table2 = $('.table-siswa').DataTable({
            serverSide: true,
            autoWidth: false,
            responsive: true,
            paging: false, // Tetap aktifkan pagination agar pengguna bisa memilih
            pageLength: 50,
            language: {
                "processing": "Mohon bersabar..."
            },
            ajax: {
                url: '{{ route('rombel.getDataSiswa', $rombel->id) }}',
            },
            columns: [{
                    data: 'DT_RowIndex',
                    searchable: false,
                    sortable: false
                },
                {
                    data: 'nama_siswa'
                },
                {
                    data: 'nisn_siswa'
                },
                {
                    data: 'aksi',
                    sortable: false,
                    searchable: false
                },
            ],
            dom: 'Brt',
            bSort: false,
        });

        function edit(url) {
            Swal.fire({
                title: 'Mohon Tunggu',
                text: 'Sedang memproses...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    window.location.href = url;
                }
            });
        }

        function modalTambahSiswa() {
            $(modal).modal('show');
        }

        // Checkbox "Select All"
        $('#selectAll').on('click', function() {
            $('.select-siswa').prop('checked', $(this).prop('checked'));
        });

        // Jika salah satu checkbox siswa tidak dicentang, hapus centang dari "Select All"
        $(document).on('click', '.select-siswa', function() {
            if ($('.select-siswa:checked').length === $('.select-siswa').length) {
                $('#selectAll').prop('checked', true);
            } else {
                $('#selectAll').prop('checked', false);
            }
        });

        $(document).ready(function() {
            // Event listener untuk Select All
            $("#selectAll").change(function() {
                $(".select-siswa").prop("checked", this.checked);
            });

            // Fungsi untuk menambahkan siswa
            function tambahSiswa() {
                let selectedSiswa = [];
                let rombelId = '{{ $rombel->id }}'

                // Ambil semua checkbox yang dipilih
                $(".select-siswa:checked").each(function() {
                    selectedSiswa.push($(this).val());
                });

                // Validasi: Pastikan ada yang dipilih
                if (selectedSiswa.length === 0) {
                    Swal.fire({
                        icon: "warning",
                        title: "Oops...",
                        text: "Silakan pilih minimal satu siswa!",
                    });
                    return;
                }

                // Tampilkan loading Swal
                Swal.fire({
                    title: "Menambahkan Siswa...",
                    text: "Mohon tunggu sebentar",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Kirim data dengan AJAX
                $.ajax({
                    url: "{{ route('rombel.addSiswa') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        siswa_ids: selectedSiswa,
                        rombel_id: rombelId
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: "success",
                                title: "Berhasil!",
                                text: "Siswa berhasil ditambahkan.",
                                timer: 2000,
                                showConfirmButton: false
                            });
                            table1.ajax.reload();
                            table2.ajax.reload();
                            $('#selectAll').prop('checked', false);
                            $("#modalTambahSiswa").modal("hide"); // Tutup modal
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Gagal!",
                                text: "Terjadi kesalahan saat menambahkan siswa.",
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: "Terjadi kesalahan jaringan. Silakan coba lagi!",
                        });
                    }
                });
            }

            // Event listener tombol Tambah Siswa
            $("#btnTambahSiswa").on("click", function() {
                tambahSiswa();
            });
        });

        function hapusSiswa(siswaId) {
            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Siswa ini akan dihapus secara permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Menghapus Siswa...",
                        text: "Mohon tunggu sebentar",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Kirim request DELETE via AJAX
                    $.ajax({
                        url: "{{ route('siswa.rombel.delete') }}",
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}",
                            siswa_id: siswaId
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Berhasil!",
                                    text: "Siswa berhasil dihapus.",
                                    timer: 2000,
                                    showConfirmButton: false
                                });

                                // Reload atau hapus elemen dari tabel
                                // $("#row_" + siswaId).remove();

                                table1.ajax.reload();
                                table2.ajax.reload();
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: "Gagal!",
                                    text: "Terjadi kesalahan saat menghapus siswa.",
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: "error",
                                title: "Oops...",
                                text: "Terjadi kesalahan jaringan. Silakan coba lagi!",
                            });
                        }
                    });
                }
            });
        }

        // Tambahkan event listener untuk tombol hapus yang ada di dalam tabel
        $(document).on("click", ".btn-hapus-siswa", function() {
            let siswaId = $(this).data("id");
            hapusSiswa(siswaId);
        });
    </script>
@endpush

@extends('layouts.app')

@section('title', 'Transaksi Tarik Tabungan')

@section('subtitle', 'Transaksi Tarik Tabungan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">@yield('title')</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <x-card>
                <x-slot name="header">
                    <h3 class="card-title">@yield('subtitle')</h3>
                    <div class="card-tools">
                        <div class="d-flex align-items-center gap-2"> <!-- Tambahkan gap antara elemen -->
                            <div class="form-group mb-0 mr-3"> <!-- Hapus margin bawah agar sejajar -->
                                <select id="filter-rombel" class="form-control form-control-sm">
                                    <option value="">Semua Rombel</option>
                                    @foreach ($rombels as $rombel)
                                        <option value="{{ $rombel->id }}">{{ $rombel->kelas->nama_kelas }}
                                            {{ $rombel->nama_rombel }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button onclick="addForm(`{{ route('tarik-tabungan.store') }}`)" type="button"
                                class="btn btn-success btn-sm"><i class="fas fa-plus-circle"></i> Tambah Data</button>
                        </div>
                    </div>
                </x-slot>

                <x-table>
                    <x-slot name="thead">
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Invoice</th>
                        <th>Type</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Jumlah</th>
                        <th>Aksi</th>
                    </x-slot>
                </x-table>
            </x-card>
        </div>
    </div>
    @include('admin.tabungan.tarik.form')
@endsection

@include('includes.datatables')

@push('scripts')
    <script>
        let table;
        let modal = '#modal-form';
        let button = '#submitBtn';

        table = $('.table').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            responsive: true,
            ajax: {
                url: '{{ route('tarik.tabungan.data') }}',
                data: function(d) {
                    d.rombel_id = $('#filter-rombel').val(); // Kirim nilai rombel_id dari select filter
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'tanggal',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'kode',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'type',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'nama_siswa',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'kelas',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'jumlah',
                    orderable: true,
                    searchable: false,
                    className: 'text-right'
                },
                {
                    data: 'aksi',
                    orderable: true,
                    searchable: false,
                }
            ],
        })

        // Event listener untuk filter rombel
        $('#filter-rombel').change(function() {
            table.ajax.reload(); // Reload data berdasarkan pilihan rombel
        });

        function addForm(url, title = "Transaksi Tarik Tabungan") {
            // Tampilkan loading dengan SweetAlert2
            Swal.fire({
                title: 'Mohon Tunggu',
                text: 'Sedang memproses...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Tunggu beberapa milidetik agar SweetAlert2 terlihat
            setTimeout(() => {
                Swal.close(); // Tutup loading

                // Pastikan modal sudah dideklarasikan
                let modal = '#modal-form';

                $(modal).modal('show'); // Tampilkan modal
                $(`${modal} .modal-title`).text(title);
                $(`${modal} form`).attr('action', url);
                $(`${modal} [name=_method]`).val('post');

                resetForm(`${modal} form`); // Reset form
            }, 500); // Delay 500ms untuk efek transisi
        }

        function submitForm(originalForm) {
            $(button).prop('disabled', true);

            // Menampilkan Swal loading
            Swal.fire({
                title: 'Mohon Tunggu...',
                text: 'Sedang memproses data',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); // Menampilkan animasi loading
                }
            });

            $.ajax({
                url: $(originalForm).attr('action'),
                type: $(originalForm).attr('method') || 'POST', // Gunakan method dari form
                data: new FormData(originalForm),
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData: false,
                success: function(response, textStatus, xhr) {
                    Swal.close(); // Tutup Swal Loading

                    if (xhr.status === 201 || xhr.status === 200) {
                        $(modal).modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 3000
                        }).then(() => {
                            $(button).prop('disabled', false);
                            table.ajax.reload(); // Reload DataTables
                        });
                    }
                },
                error: function(xhr) {
                    Swal.close(); // Tutup Swal Loading
                    $(button).prop('disabled', false);

                    let errorMessage = "Terjadi kesalahan!";
                    if (xhr.responseJSON?.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Oops! Gagal',
                        text: errorMessage,
                        showConfirmButton: false,
                        timer: 3000,
                    });

                    if (xhr.status === 422) {
                        loopErrors(xhr.responseJSON.errors);
                    }
                }
            });
        }

        function deleteData(url, name) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: true,
            });

            swalWithBootstrapButtons.fire({
                title: 'Delete Data!',
                text: 'Apakah Anda yakin ingin menghapus ' + name + ' ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#aaa',
                confirmButtonText: 'Iya!',
                cancelButtonText: 'Batalkan',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan Swal loading sebelum menghapus
                    Swal.fire({
                        title: 'Menghapus...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        type: "DELETE",
                        url: url,
                        dataType: "json",
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 3000
                            }).then(() => {
                                table.ajax.reload(); // Reload DataTables setelah penghapusan
                            });
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops! Gagal',
                                text: xhr.responseJSON ? xhr.responseJSON.message :
                                    'Terjadi kesalahan!',
                                showConfirmButton: true,
                            }).then(() => {
                                table.ajax.reload(); // Reload tabel jika terjadi error
                            });
                        }
                    });
                }
            });
        }
    </script>
@endpush

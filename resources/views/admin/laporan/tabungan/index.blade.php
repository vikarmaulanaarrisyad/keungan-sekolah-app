@extends('layouts.app')

@section('title', 'Cetak Laporan Tabungan')

@section('subtitle', 'Cetak Laporan Tabungan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">@yield('title')</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <x-card>
                <x-slot name="header">
                    <h3 class="card-title">Cetak Tabungan Siswa</h3>
                    <div class="card-tools">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-3">
                                <select id="filter-bulan" class="form-control form-control-sm">
                                    @foreach (range(1, 12) as $month)
                                        <option value="{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}"
                                            {{ $month == date('m') ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <select id="filter-tahun" class="form-control form-control-sm">
                                    @foreach (range(date('Y') - 5, date('Y')) as $year)
                                        <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <select id="filter-siswa" class="form-control form-control-sm">
                                    <option value="">Semua Siswa</option>
                                    @foreach ($siswas as $siswa)
                                        <option value="{{ $siswa->id }}">{{ $siswa->nama_siswa }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <button id="btn-cetak"
                                    class="btn btn-primary btn-sm w-100 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-print me-1"></i> Cetak
                                </button>
                            </div>
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
                    </x-slot>
                </x-table>
            </x-card>
        </div>
    </div>
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
                url: '{{ route('cetak-tabungan.data') }}',
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
                    orderable: false,
                    searchable: false
                },
            ]
        })

        // Event listener untuk filter rombel
        $('#filter-rombel').change(function() {
            table.ajax.reload(); // Reload data berdasarkan pilihan rombel
        });

        $('#filter-bulan, #filter-tahun, #filter-siswa').change(function() {
            table.ajax.reload();
        });

        $('#btn-cetak').click(function() {
            let bulan = $('#filter-bulan').val();
            let tahun = $('#filter-tahun').val();
            let siswa_id = $('#filter-siswa').val();

            let url = '{{ route('cetak-tabungan.pdf') }}?bulan=' + bulan + '&tahun=' + tahun + '&siswa_id=' +
                siswa_id;

            window.open(url, '_blank');
        });

        function setorTabungan(url) {
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

        function tarikTabungan(url) {
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
    </script>
@endpush

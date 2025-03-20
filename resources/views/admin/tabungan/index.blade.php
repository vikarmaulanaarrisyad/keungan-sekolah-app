@extends('layouts.app')

@section('title', 'Data Tabungan')

@section('subtitle', 'Data Tabungan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">@yield('title')</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <x-card>
                <x-slot name="header">
                    <h3 class="card-title">Tabungan Siswa</h3>
                    <div class="card-tools">
                        <div class="d-flex align-items-center">
                            <div>
                                <button onclick="setorTabungan(`{{ route('setor-tabungan.index') }}`)" type="button"
                                    class="btn btn-success btn-sm"><i class="fas fa-download"></i> Setor Tabungan</button>

                                <button onclick="tarikTabungan(`{{ route('tarik-tabungan.index') }}`)"
                                    class="btn btn-sm btn-success">
                                    <i class="fas fa-upload"></i> Tarik Tabungan
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
            processing: false,
            serverSide: true,
            autoWidth: false,
            responsive: true,
            ajax: {
                url: '{{ route('tabungan.data') }}'
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

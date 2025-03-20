<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Tabungan Bulanan Siswa</title>
    <style>
        @page {
            margin: 0;
            size: A6 portrait;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            text-align: center;
            margin: 0;
            padding: 5px 20px;
            height: auto;
        }

        .container {
            width: 100%;
            height: auto;
            box-sizing: border-box;
            page-break-after: always;
        }

        h2 {
            font-size: 14px;
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 10px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 4px;
        }

        th {
            background-color: #ccc;
        }

        .info-table td {
            border: none;
            text-align: left;
            padding: 2px;
        }
    </style>
</head>

<body>

    @if ($query->count() == 1)
        @php $siswa = $query->first()->siswa ?? null; @endphp
        <div class="container">
            <h2>Kartu Tabungan Bulanan Siswa</h2>
            <p class="subtitle">.</p>
            <table class="info-table">
                <tr>
                    <td>Nama Siswa</td>
                    <td>{{ $siswa->nama_siswa ?? 'Nama tidak tersedia' }}</td>
                </tr>
                <tr>
                    <td>Kelas</td>
                    <td>{{ $siswa->kelas->nama_kelas }}</td>
                </tr>
                <tr>
                    <td>Wali Kelas</td>
                    <td>_________________</td>
                </tr>
            </table>

            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kredit</th>
                        <th>Debit</th>
                        <th>Saldo</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($query as $tabungan)
                        <tr>
                            <td>{{ $tabungan->tanggal }}</td>
                            <td>
                                @if ($tabungan->type == 'setor')
                                    {{ format_uang($tabungan->jumlah) }}
                                @else
                                    <div style="text-align: center;">-</div>
                                @endif
                            </td>
                            <td>
                                @if ($tabungan->type == 'tarik')
                                    {{ format_uang($tabungan->jumlah) }}
                                @else
                                    <div style="text-align: center;">-</div>
                                @endif
                            </td>
                            <td>{{ $tabungan->sum('jumlah') }}</td>
                            <td>{{ $tabungan->keterangan }}</td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    @else
        @foreach ($query->groupBy('siswa_id') as $siswa_id => $data)
            @php $siswa = $data->first()->siswa ?? null; @endphp
            <div class="container">
                <h2>Kartu Tabungan Bulanan Siswa</h2>
                <p class="subtitle"></p>
                <table class="info-table">
                    <tr>
                        <td>Nama Siswa</td>
                        <td>: {{ $siswa->nama_siswa ?? 'Nama tidak tersedia' }}</td>
                    </tr>
                    <tr>
                        <td>Kelas</td>
                        <td>: {{ $siswa->kelas->nama_kelas }}</td>
                    </tr>
                    <tr>
                        <td>Wali Kelas</td>
                        <td>: {{ optional(optional(optional($siswa->kelas)->rombel)->guru)->nama_guru ?? '-' }}</td>

                    </tr>
                </table>

                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kredit</th>
                            <th>Debit</th>
                            <th>Saldo</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($query as $tabungan)
                            <tr>
                                <td>{{ $tabungan->tanggal }}</td>
                                <td style="text-align:right">
                                    @if ($tabungan->type == 'setor')
                                        {{ format_uang($tabungan->jumlah) }}
                                    @else
                                        <div style="text-align: center;">-</div>
                                    @endif
                                </td>
                                <td style="text-align:right">
                                    @if ($tabungan->type == 'tarik')
                                        {{ format_uang($tabungan->jumlah) }}
                                    @else
                                        <div style="text-align: center;">-</div>
                                    @endif
                                </td>
                                <td style="text-align: right;"> {{ format_uang($tabungan->saldo_akhir) }}</td>
                                <td style="text-align: center;">{{ $tabungan->keterangan ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    @endif

</body>

</html>

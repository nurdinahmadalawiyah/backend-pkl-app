<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        @page {
            margin-top: 85.03937pt;
            margin-bottom: 56.69291pt;
            margin-left: 85.03937pt;
            margin-right: 56.69291pt;
        }

        body {
            font-family: Arial, sans-serif;
        }

        h3 {
            align-items: center;
        }

        #text-biodata {
            font-size: 18px;
        }

        .table-cell {
            width: 150px;
            white-space: nowrap;
        }

        .table-cell:after {
            content: "\00a0\00a0\00a0\00a0";
            /* Tambahkan empat karakter spasi */
        }

        .table-jurnal {
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
            border: 1px solid #000000;
        }

        .table-sign {
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
        }

        .td-jurnal {
            border: 1px solid #000000;
            text-align: center;
            padding: 1px;
        }
    </style>
</head>

<body>
    <center>
        <p id="text-biodata">DAFTAR HADIR MAHASISWA PKL</p>
    </center>
    <table>
        <tr>
            <td class="table-cell">Nama Mahasiswa</td>
            <td>: @if($data_kehadiran){{ $data_kehadiran->nama }}@endif </td>
        </tr>
        <tr>
            <td class="table-cell">NIM</td>
            <td>: @if($data_kehadiran){{ $data_kehadiran->nim }}@endif</td>
        </tr>
        <tr>
            <td class="table-cell">Program Studi</td>
            <td>: @if($data_kehadiran){{ $data_kehadiran->nama_prodi }}@endif </td>
        </tr>
    </table>
    <br>
    <table class="table-jurnal">
        <tr>
            <td class="td-jurnal" style="width: 10%">No</td>
            <td class="td-jurnal" style="width: 15%">Minggu Ke / Tanggal</td>
            <td class="td-jurnal">Senin</td>
            <td class="td-jurnal">Selasa</td>
            <td class="td-jurnal">Rabu</td>
            <td class="td-jurnal">Kamis</td>
            <td class="td-jurnal">Jum'at</td>
            <td class="td-jurnal">Sabtu</td>
        </tr>
        @php
            setlocale(LC_TIME, 'id');
        @endphp
        @foreach ($grouped as $data)
            <tr>
                <td class="td-jurnal">{{ $loop->iteration }}</td>
                <td class="td-jurnal">
                    {{ $data['minggu'] . ' / ' }}
                    @if (count($data['data_kehadiran']) == 1)
                        {{ strftime('%e %B %Y', strtotime($data['data_kehadiran']->first()['hari_tanggal'])) }}
                    @else
                        {{ strftime('%e', strtotime($data['data_kehadiran']->first()['hari_tanggal'])) }}
                        - {{ strftime('%e %B %Y', strtotime($data['data_kehadiran']->last()['hari_tanggal'])) }}
                    @endif
                </td>
                @for ($i = 0; $i < 6; $i++)
                    <td class="td-jurnal">
                        @foreach ($data['data_kehadiran'] as $kehadiran)
                            @if (date('N', strtotime($kehadiran['hari_tanggal'])) == $i + 1)
                                <img src="{{ $kehadiran['tanda-tangan'] }}" alt="Tanda Tangan"
                                    style="max-width:100%; max-height:100%;">
                            @endif
                        @endforeach
                    </td>
                @endfor

                {{-- @for ($i = 0; $i < 6; $i++)
                    <td class="td-jurnal">
                        @if (!empty($data['data_kehadiran'][$i]['tanda-tangan']))
                            <img src="{{ $data['data_kehadiran'][$i]['tanda-tangan'] }}" alt="Tanda Tangan"
                                style="max-width:100%; max-height:100%;">
                        @endif
                    </td>
                @endfor --}}
            </tr>
        @endforeach
    </table>
    <br>
    <table class="table-sign">
        <tr>
            <td>
                <div style="text-align: left">
                    <div style="display: inline-block; text-align: center">
                        Pembimbing
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <b><u>@if($data_kehadiran){{ $data_kehadiran->nama_pembimbing }}@endif</u></b><br>
                        NIK. @if($data_kehadiran){{ $data_kehadiran->nik }}@endif
                    </div>
                </div>
            </td>
            <td>
                <div style="text-align: right">
                    <div style="display: inline-block; text-align: center">
                        Mahasiswa
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <b><u>@if($data_kehadiran){{ $data_kehadiran->nama }}@endif</u></b><br>
                        NIM. @if($data_kehadiran){{ $data_kehadiran->nim }}@endif
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>

</html>

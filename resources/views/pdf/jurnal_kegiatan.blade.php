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

        .custom-ol {
            list-style-type: none;
            padding: 0;
        }

        .custom-ol li {
            counter-increment: custom-counter;
            margin-bottom: 0px;
        }

        .custom-ol li::before {
            content: counter(custom-counter) ")";
            margin-right: 5px;
        }

        .table-jurnal {
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
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

        .th-jurnal {
            border: 1px solid #000000;
            text-align: center;
            padding: 1px;
        }
    </style>
</head>

<body>
    <center>
        <p id="text-biodata">JURNAL KEGIATAN PKL</p>
    </center>
    <table>
        <tr>
            <td class="table-cell">Nama Mahasiswa</td>
            <td>: {{ $data_jurnal->nama }} </td>
        </tr>
        <tr>
            <td class="table-cell">Minggu Ke</td>
            <td>: {{ $grouped[0]['minggu'] }}</td>

        </tr>
        <tr>
            <td class="table-cell">NIM</td>
            <td>: {{ $data_jurnal->nim }} </td>
        </tr>
        <tr>
            <td class="table-cell">Program Studi</td>
            <td>: {{ $data_jurnal->nama_prodi }} </td>
        </tr>
        <tr>
            <td class="table-cell">Nama Industri/Instansi</td>
            <td>: {{ $data_jurnal->nama_industri }} </td>
        </tr>
        <tr>
            <td class="table-cell">Alamat</td>
            <td>: {{ $data_jurnal->alamat_kantor }} </td>
        </tr>
    </table>
    <br>
    <table class="table-jurnal">
        <tr>
            <th class="th-jurnal" style="width: 10%">No</th>
            <th class="th-jurnal" style="width: 25%">Hari/Tanggal</th>
            <th class="th-jurnal" style="width: 30%">Bidang Pekerjaan</th>
            <th class="th-jurnal" style="width: 35%">Keterangan</th>
        </tr>
        @php
            $counter = 1;
            setlocale(LC_TIME, 'id');
        @endphp
        @foreach ($grouped as $data)
            <tr>
                <td class="td-jurnal">{{ $counter++ }}</td>
                <td class="td-jurnal">{{ strftime('%A, %d %B %Y', strtotime($data['tanggal'])) }}</td>
                <td class="td-jurnal">{{ $data['bidang_pekerjaan'] }}</td>
                <td style="border: 1px solid #000000; padding: 4px;">
                    @php
                        $keterangan = explode("\n", $data['keterangan']);
                    @endphp
                        @foreach ($keterangan as $item)
                        - {{ $item }} <br> 
                        @endforeach
                </td>
            </tr>
        @endforeach
    </table>
    <br>
    <table class="table-sign">
        <tr>
            <td>
                <div style="text-align: left">
                    <div style="display: inline-block; text-align: center">
                        Cimahi, <?php echo date('d F Y'); ?> <br>
                        Pembimbing Industri
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <b><u>{{ $data_jurnal->nama_pembimbing }}</u></b><br>
                        NIK. {{ $data_jurnal->nik }}
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>


</html>

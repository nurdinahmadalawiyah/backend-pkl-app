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

        .table-tenaga-kerja {
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
        }

        .table-sign {
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
        }

        .td-tenaga-kerja {
            border: 1px solid #000000;
            text-align: left;
            padding: 1px;
        }

        .th-tenaga-kerja {
            border: 1px solid #000000;
            text-align: center;
            padding: 1px;
        }
    </style>
</head>

<body>
    <center>
        <p id="text-biodata">BIODATA INDUSTRI</p>
    </center>
    <table>
        <tr>
            <td style="padding-bottom: 5px"><b>IDENTITAS INDUSTRI</b></td>
        </tr>
        <tr>
            <td class="table-cell">Nama Industri</td>
            <td>: {{ $biodata_industri->nama_industri }}</td>
        </tr>
        <tr>
            <td class="table-cell">Nama Direktur/Pimpinan</td>
            <td>: {{ $biodata_industri->nama_pimpinan }}</td>
        </tr>
        <tr>
            <td class="table-cell">Alamat Kantor</td>
            <td>: {{ $biodata_industri->alamat_kantor }}</td>
        </tr>
        <tr>
            <td class="table-cell">No.Telepon/FAX</td>
            <td>: {{ $biodata_industri->no_telp_fax }}</td>
        </tr>
        <tr>
            <td class="table-cell">Contact Person</td>
            <td>: {{ $biodata_industri->contact_person }}</td>
        </tr>
    </table>
    <br>
    <table>
        <tr>
            <td style="padding-bottom: 5px"><b>AKTIVITAS</b></td>
        </tr>
        <tr>
            <td class="table-cell">a. Bidang Usaha/Jasa</td>
            <td>:</td>
        </tr>
        <tr>
            <td style="text-align: left; padding-left: 20px">
                @php
                    $bidang_usaha_jasa = explode("\n", $biodata_industri->bidang_usaha_jasa);
                    $counter = 1;
                @endphp
                    @foreach ($bidang_usaha_jasa as $item)
                    {{ $counter++ }})  {{ $item }} <br>
                    @endforeach
            </td>
        </tr>
        <tr>
            <td class="table-cell">b. Spesialisasi Produksi/Jasa</td>
            <td>: {{ $biodata_industri->spesialisasi_produksi_jasa }}</td>
        </tr>
        <tr>
            <td class="table-cell">c. Kapasitas Produksi</td>
            <td>: {{ $biodata_industri->kapasitas_produksi }}</td>
        </tr>
        <tr>
            <td class="table-cell">d. Jangkauan Pemasaran</td>
            <td>: {{ $biodata_industri->jangkauan_pemasaran }}</td>
        </tr>
    </table>
    <br>

    <table class="table-tenaga-kerja">
        <tr>
            <td style="padding-bottom: 5px"><b>&nbsp;&nbsp;TENAGA KERJA</b></td>
        </tr>
        <tr>
            <th class="th-tenaga-kerja">TINGKAT PENDIDIKAN</th>
            <th class="th-tenaga-kerja">JUMLAH</th>
        </tr>
        <tr>
            <td class="td-tenaga-kerja">SD</td>
            <td class="td-tenaga-kerja">
                <center>{{ $biodata_industri->jumlah_tenaga_kerja_sd }}</center>
            </td>
        </tr>
        <tr>
            <td class="td-tenaga-kerja">SLTP</td>
            <td class="td-tenaga-kerja">
                <center>{{ $biodata_industri->jumlah_tenaga_kerja_sltp }}</center>
            </td>
        </tr>
        <tr>
            <td class="td-tenaga-kerja">SMK: STM</td>
            <td class="td-tenaga-kerja">
                <center>{{ $biodata_industri->jumlah_tenaga_kerja_smk }}</center>
            </td>
        </tr>
        <tr>
            <td class="td-tenaga-kerja">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SMEA</td>
            <td class="td-tenaga-kerja">
                <center></center>
            </td>
        </tr>
        <tr>
            <td class="td-tenaga-kerja">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SMKK/SMTK</td>
            <td class="td-tenaga-kerja">
                <center></center>
            </td>
        </tr>
        <tr>
            <td class="td-tenaga-kerja">SLTA Non SMK</td>
            <td class="td-tenaga-kerja">
                <center>{{ $biodata_industri->jumlah_tenaga_kerja_slta }}</center>
            </td>
        </tr>
        <tr>
            <td class="td-tenaga-kerja">Sarjana Muda</td>
            <td class="td-tenaga-kerja">
                <center>{{ $biodata_industri->jumlah_tenaga_kerja_sarjana_muda }}</center>
            </td>
        </tr>
        <tr>
            <td class="td-tenaga-kerja">Sarjana Magister</td>
            <td class="td-tenaga-kerja">
                <center>{{ $biodata_industri->jumlah_tenaga_kerja_sarjana_magister }}</center>
            </td>
        </tr>
        <tr>
            <td class="td-tenaga-kerja">Doktor</td>
            <td class="td-tenaga-kerja">
                <center>{{ $biodata_industri->jumlah_tenaga_kerja_sarjana_doktor }}</center>
            </td>
        </tr>
    </table>
    <table class="table-tenaga-kerja">
        <tr>
            <td> Jumlah Tenaga Kerja
                {{ $biodata_industri->jumlah_tenaga_kerja_sd +
                    $biodata_industri->jumlah_tenaga_kerja_sltp +
                    $biodata_industri->jumlah_tenaga_kerja_smk +
                    $biodata_industri->jumlah_tenaga_kerja_slta +
                    $biodata_industri->jumlah_tenaga_kerja_sarjana_muda +
                    $biodata_industri->jumlah_tenaga_kerja_sarjana_magister +
                    $biodata_industri->jumlah_tenaga_kerja_sarjana_doktor }}
                orang
            </td>
        </tr>
        <tr>
            @php
                setlocale(LC_TIME, 'id');
                $tanggal = strftime('%e %B %Y');
            @endphp
            <td style="text-align: right">Cimahi, <?php echo $tanggal; ?></td>
        </tr>
    </table>

    <table class="table-sign">
        <tr>
            <td style="padding-bottom: 10px" colspan="2"> Mengetahui </td>
        </tr>
        <tr>
            <td>
                <div style="text-align: left">
                    <div style="display: inline-block; text-align: center">
                        Direktur / Pembimbing Industri
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <b><u>{{ $biodata_industri->nama_pembimbing }}</u></b><br>
                        NIK. {{ $biodata_industri->nik }}
                    </div>
                </div>
            </td>
            <td>
                <div style="text-align: right">
                    <div style="display: inline-block; text-align: center">
                        Mahasiswa PKL
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <b><u>{{ $biodata_industri->nama }}</u></b><br>
                        NIM. {{ $biodata_industri->nim }}
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>

</html>

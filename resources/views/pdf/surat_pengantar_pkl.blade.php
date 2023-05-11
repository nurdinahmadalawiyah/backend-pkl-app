<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Document</title>
    <style>
        @page {
            margin-top: 28.346455pt;
            margin-bottom: 56.69291pt;
            margin-left: 56.69291pt;
            margin-right: 56.69291pt;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
        }


        hr {
            border-top: 3px solid black;
            margin-top: 1em;
            margin-bottom: 1em;
        }

        #text-biodata {
            font-size: 18px;
        }

        .text-header {
            font-family: Arial, sans-serif;
            font-size: 35px;
            font-weight: bold;
            color: #150AD0;
            text-align: center;
            padding-left: 15px;
            padding-right: 15px;
        }

        .text-header-2 {
            font-family: Arial, sans-serif;
            font-size: 13px;
            text-align: center;
        }

        .table-mahasiswa {
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
        }

        .table-sign {
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
        }

        .td-mahasiswa {
            border: 1px solid #000000;
            text-align: center;
            padding: 6px;
        }

        .th-mahasiswa {
            border: 1px solid #000000;
            text-align: center;
            padding: 1px;
        }
    </style>
</head>

<body>
    {{-- <table border="1" style="width: 100%">
        <tr>
            <td style="text-align: right;" rowspan="3">
                <img src="{{ asset('assets/images/logo_tedc.png') }}" alt="Logo TEDC"
                    style="max-width:65; max-height:65;">
            </td>
        </tr>
        <tr>
            <td class="text-header">
                <img src="{{ asset('assets/images/text-header.png') }}" alt="Logo TEDC"
                    style="max-width:100%; max-height:100%;">
            </td>
        </tr>
        <tr>
            <td class="text-header-2">Jl. Politeknik - Pesantren Km 2 Cibabat - Cimahi Utara 40513 Telp/Fax. (022)
                6645951 <br>
                Email: info@poltektedc.ac.id / public@poltektedc.ac.id Website: http://www.poltektedc.ac.id</td>
        </tr>
    </table>
    <hr> --}}
    <table style="width: 100%">
        <tr>
            <td style="text-align: right;">
                <img src="{{ asset('assets/images/header.png') }}" style="max-width:100%; max-height:100%;">
            </td>
        </tr>
    </table>
    <br>
    <table style="width: 100%">
        <tr>
            @php
                setlocale(LC_TIME, 'id');
            @endphp
            <td style="text-align: right;" colspan="2">Cimahi, <?php echo strftime('%e %B %Y'); ?></td>
        </tr>
        <tr>
            <td style="width: 10%">Nomor </td>
            <td>: 299.7/PKL/TEDC-BAA/<?php
            function romanNumerals($num)
            {
                $n = intval($num);
                $result = '';
                // Define a lookup array that contains all of the Roman numerals.
                $lookup = [
                    'M' => 1000,
                    'CM' => 900,
                    'D' => 500,
                    'CD' => 400,
                    'C' => 100,
                    'XC' => 90,
                    'L' => 50,
                    'XL' => 40,
                    'X' => 10,
                    'IX' => 9,
                    'V' => 5,
                    'IV' => 4,
                    'I' => 1,
                ];
                foreach ($lookup as $roman => $value) {
                    // Determine the number of matches.
                    $matches = intval($n / $value);
                    // Add the same number of characters to the result.
                    $result .= str_repeat($roman, $matches);
                    // Subtract the current value from the number.
                    $n = $n % $value;
                }
                // The Roman numeral should be built.
                return $result;
            }
            $bulan = date('m');
            $romawi = romanNumerals($bulan);
            echo $romawi; // Output: bulan dalam angka Romawi
            ?>/<?php echo date('Y'); ?></td>
        </tr>
        <tr>
            <td style="width: 10%">Hal </td>
            <td>: Permohonan Peraktik Kerja Lapangan (PKL)</td>
        </tr>
    </table>
    <br>
    <br>
    <table style="width: 90%">
        <td style="font-weight: bold;">
            Kepada Yth,<br>
            Direktur / Pimpinan {{ $pengajuan_pkl->nama_perusahaan }} <br>
            Di <br>
            {{ $pengajuan_pkl->alamat_perusahaan }} <br>
        </td>
    </table>
    <br>
    <table style="width: 100%">
        <tr>
            @php
                $counter = 1;
                setlocale(LC_TIME, 'id');
            @endphp
            <td style="text-align: justify;">
                Dengan Hormat, <br>
                Dalam rangka meningkatkan kemampuan keterampilan mahasiswa serta mempelajari budaya dunia kerja
                perusahaan/instansi maka kami mohon bantuan Bapak/Ibu Pimpinan agar dapat memberikan kesempatan kepada
                mahasiswa kami untuk melaksanakan Praktik Kerja Lapangan (PKL) di Perusahaan/instansi Bapak/Ibu yang
                dijadwalkan pelaksanaannya mulai <b>{{ strftime('%d %B %Y', strtotime($pengajuan_pkl->tanggal_mulai)) }}
                    s.d. {{ strftime('%d %B %Y', strtotime($pengajuan_pkl->tanggal_selesai)) }}</b>.
            </td>
        </tr>
        <br>
        <tr>
            <td style="text-align: justify;">
                Adapun nama mahasiswa yang akan melaksanakan PKL adalah sebagai berikut:
            </td>
        </tr>
    </table>
    <br>
    <table class="table-mahasiswa" style="width: 100%">
        <tr>
            <th class="th-mahasiswa" style="width: 7%">NO</th>
            <th class="th-mahasiswa" style="width: 15%">NIM</th>
            <th class="th-mahasiswa" style="width: 33%">NAMA</th>
            <th class="th-mahasiswa" style="width: 25%">PROGRAM STUDI / KONSENTRASI</th>
            <th class="th-mahasiswa" style="width: 15%">SEMESTER</th>
        </tr>
        <tr>
            <td class="td-mahasiswa">{{ $counter++ }}</td>
            <td class="td-mahasiswa">{{ $data_surat->nim }}</td>
            <td class="td-mahasiswa">{{ $data_surat->nama }}</td>
            <td class="td-mahasiswa">{{ $data_surat->nama_prodi }}</td>
            <td class="td-mahasiswa">{{ $data_surat->semester }}</td>
        </tr>
    </table>
    <br>
    <table style="width: 100%">
        <tr>
            <td style="text-align: justify;">
                Selama masa PKL Mahasiswa yang bersangkutan akan mentaati semua peraturan yang berlaku di
                perusahaan/instansi yang Bapak/Ibu pimpin. Selanjutnya sebagai alat untuk memonitor kemajuan mahasiswa
                dalam melaksanakan PKL, kami lampirkan jurnal kegiatan PKL yang harus diisi oleh mahasiswa yang
                bersangkutan dan ditandatangani oleh Pembimbing di perusahaan, sebagai bahan pertimbangan kami lampirkan
                lampirkan Transkip Nilai Mahasiswa yang bersangkutan.
            </td>
        </tr>
        <br>
        <tr>
            <td style="text-align: justify;">
                Demikian, atas perhatian dan kerjasama yang baik kami sampaikan terimakasih.
            </td>
        </tr>
    </table>
    <br>
    <table class="table-sign">
        <tr>
            <td>
                <div style="text-align: right">
                    <div style="display: inline-block; text-align: left">
                        Ketua Program Studi <br>
                        {{ $data_surat->nama_prodi }}
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <b><u>{{ $data_surat->nama_ketua_prodi }}</u></b><br>
                        NIDN. {{ $data_surat->nidn_ketua_prodi }}
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>

</html>

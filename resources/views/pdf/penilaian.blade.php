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

        #text-title {
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

        .table-nilai {
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
        }

        .table-sign {
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
        }

        .td-nilai {
            border: 1px solid #000000;
            text-align: center;
            padding: 1px;
        }

        .th-nilai {
            border: 1px solid #000000;
            text-align: center;
            padding: 1px;
        }
    </style>
</head>

<body>
    <center>
        <p id="text-title"><b>LEMBAR PENILAIAN</b></p>
        <p id="text-title"><b>INDUSTRI/INSTANSI TEMPAT PKL</b></p>
        <br>
    </center>
    <table>
        <tr>
            <td class="table-cell">Nama Mahasiswa</td>
            <td>: {{ $penilaian->nama }}</td>
        </tr>
        <tr>
            <td class="table-cell">NIM</td>
            <td>: {{ $penilaian->nim }}</td>
        </tr>
        <tr>
            <td class="table-cell">Program Studi</td>
            <td>: {{ $penilaian->nama_prodi }}</td>
        </tr>
    </table>
    <br>
    <table class="table-nilai">
        <tr>
            <th class="th-nilai" style="width: 5%;">No</th>
            <th class="th-nilai" style="width: 35%;"></th>
            <th class="th-nilai" style="width: 15%;">Sangat Baik</th>
            <th class="th-nilai" style="width: 15%;">Baik</th>
            <th class="th-nilai" style="width: 15%;">Cukup</th>
            <th class="th-nilai" style="width: 15%;">Kurang</th>
        </tr>
        @php
            $integritas = $penilaian->integritas;
            $resultIntegritas = ['SB' => '', 'B' => '', 'C' => '', 'K' => ''];
            
            if ($integritas >= 85 && $integritas <= 100) {
                $resultIntegritas['SB'] = $integritas;
            } elseif ($integritas >= 70 && $integritas < 85) {
                $resultIntegritas['B'] = $integritas;
            } elseif ($integritas >= 56 && $integritas < 70) {
                $resultIntegritas['C'] = $integritas;
            } elseif ($integritas >= 0 && $integritas < 56) {
                $resultIntegritas['K'] = $integritas;
            }
        @endphp
        <tr>
            <td class="td-nilai">1</td>
            <td style="border: 1px solid #000000;"> Memiliki integritas yang baik di lingkungan kerja</td>
            <td class="td-nilai">{{ $resultIntegritas['SB'] }}</td>
            <td class="td-nilai">{{ $resultIntegritas['B'] }}</td>
            <td class="td-nilai">{{ $resultIntegritas['C'] }}</td>
            <td class="td-nilai">{{ $resultIntegritas['K'] }}</td>
        </tr>

        @php
            $profesionalitas = $penilaian->profesionalitas;
            $resultProfesionalitas = ['SB' => '', 'B' => '', 'C' => '', 'K' => ''];
            
            if ($profesionalitas >= 85 && $profesionalitas <= 100) {
                $resultProfesionalitas['SB'] = $profesionalitas;
            } elseif ($profesionalitas >= 70 && $profesionalitas < 85) {
                $resultProfesionalitas['B'] = $profesionalitas;
            } elseif ($profesionalitas >= 56 && $profesionalitas < 70) {
                $resultProfesionalitas['C'] = $profesionalitas;
            } elseif ($profesionalitas >= 0 && $profesionalitas < 56) {
                $resultProfesionalitas['K'] = $profesionalitas;
            }
        @endphp
        <tr>
            <td class="td-nilai">2</td>
            <td style="border: 1px solid #000000;"> Mampu bekerja secara profesionla sesuai bidangnya</td>
            <td class="td-nilai">{{ $resultProfesionalitas['SB'] }}</td>
            <td class="td-nilai">{{ $resultProfesionalitas['B'] }}</td>
            <td class="td-nilai">{{ $resultProfesionalitas['C'] }}</td>
            <td class="td-nilai">{{ $resultProfesionalitas['K'] }}</td>
        </tr>

        @php
            $bahasa_inggris = $penilaian->bahasa_inggris;
            $resultBahasaInggris = ['SB' => '', 'B' => '', 'C' => '', 'K' => ''];
            
            if ($bahasa_inggris >= 85 && $bahasa_inggris <= 100) {
                $resultBahasaInggris['SB'] = $bahasa_inggris;
            } elseif ($bahasa_inggris >= 70 && $bahasa_inggris < 85) {
                $resultBahasaInggris['B'] = $bahasa_inggris;
            } elseif ($bahasa_inggris >= 56 && $bahasa_inggris < 70) {
                $resultBahasaInggris['C'] = $bahasa_inggris;
            } elseif ($bahasa_inggris >= 0 && $bahasa_inggris < 56) {
                $resultBahasaInggris['K'] = $bahasa_inggris;
            }
        @endphp
        <tr>
            <td class="td-nilai">3</td>
            <td style="border: 1px solid #000000;">Cakap dalam berkomunikasi bahasa inggris</td>
            <td class="td-nilai">{{ $resultBahasaInggris['SB'] }}</td>
            <td class="td-nilai">{{ $resultBahasaInggris['B'] }}</td>
            <td class="td-nilai">{{ $resultBahasaInggris['C'] }}</td>
            <td class="td-nilai">{{ $resultBahasaInggris['K'] }}</td>
        </tr>

        @php
            $teknologi_informasi = $penilaian->teknologi_informasi;
            $resultTeknologiInformasi = ['SB' => '', 'B' => '', 'C' => '', 'K' => ''];
            
            if ($teknologi_informasi >= 85 && $teknologi_informasi <= 100) {
                $resultTeknologiInformasi['SB'] = $teknologi_informasi;
            } elseif ($teknologi_informasi >= 70 && $teknologi_informasi < 85) {
                $resultTeknologiInformasi['B'] = $teknologi_informasi;
            } elseif ($teknologi_informasi >= 56 && $teknologi_informasi < 70) {
                $resultTeknologiInformasi['C'] = $teknologi_informasi;
            } elseif ($teknologi_informasi >= 0 && $teknologi_informasi < 56) {
                $resultTeknologiInformasi['K'] = $teknologi_informasi;
            }
        @endphp
        <tr>
            <td class="td-nilai">4</td>
            <td style="border: 1px solid #000000;"> Mampu mengaplikasikan teknologi informasi</td>
            <td class="td-nilai">{{ $resultTeknologiInformasi['SB'] }}</td>
            <td class="td-nilai">{{ $resultTeknologiInformasi['B'] }}</td>
            <td class="td-nilai">{{ $resultTeknologiInformasi['C'] }}</td>
            <td class="td-nilai">{{ $resultTeknologiInformasi['K'] }}</td>
        </tr>

        @php
            $komunikasi = $penilaian->komunikasi;
            $resultKomunikasi = ['SB' => '', 'B' => '', 'C' => '', 'K' => ''];
            
            if ($komunikasi >= 85 && $komunikasi <= 100) {
                $resultKomunikasi['SB'] = $komunikasi;
            } elseif ($komunikasi >= 70 && $komunikasi < 85) {
                $resultKomunikasi['B'] = $komunikasi;
            } elseif ($komunikasi >= 56 && $komunikasi < 70) {
                $resultKomunikasi['C'] = $komunikasi;
            } elseif ($komunikasi >= 0 && $komunikasi < 56) {
                $resultKomunikasi['K'] = $komunikasi;
            }
        @endphp
        <tr>
            <td class="td-nilai">5</td>
            <td style="border: 1px solid #000000;"> Mampu berkomunikasi dengan teman sejawat atau atasan</td>
            <td class="td-nilai">{{ $resultKomunikasi['SB'] }}</td>
            <td class="td-nilai">{{ $resultKomunikasi['B'] }}</td>
            <td class="td-nilai">{{ $resultKomunikasi['C'] }}</td>
            <td class="td-nilai">{{ $resultKomunikasi['K'] }}</td>
        </tr>

        @php
            $kerja_sama = $penilaian->kerja_sama;
            $resultKerjaSama = ['SB' => '', 'B' => '', 'C' => '', 'K' => ''];
            
            if ($kerja_sama >= 85 && $kerja_sama <= 100) {
                $resultKerjaSama['SB'] = $kerja_sama;
            } elseif ($kerja_sama >= 70 && $kerja_sama < 85) {
                $resultKerjaSama['B'] = $kerja_sama;
            } elseif ($kerja_sama >= 56 && $kerja_sama < 70) {
                $resultKerjaSama['C'] = $kerja_sama;
            } elseif ($kerja_sama >= 0 && $kerja_sama < 56) {
                $resultKerjaSama['K'] = $kerja_sama;
            }
        @endphp
        <tr>
            <td class="td-nilai">6</td>
            <td style="border: 1px solid #000000;"> Mampu bekerjasama dengan teman sejawat/tim</td>
            <td class="td-nilai">{{ $resultKerjaSama['SB'] }}</td>
            <td class="td-nilai">{{ $resultKerjaSama['B'] }}</td>
            <td class="td-nilai">{{ $resultKerjaSama['C'] }}</td>
            <td class="td-nilai">{{ $resultKerjaSama['K'] }}</td>
        </tr>

        @php
            $organisasi = $penilaian->organisasi;
            $resultOrganisasi = ['SB' => '', 'B' => '', 'C' => '', 'K' => ''];
            
            if ($organisasi >= 85 && $organisasi <= 100) {
                $resultOrganisasi['SB'] = $organisasi;
            } elseif ($organisasi >= 70 && $organisasi < 85) {
                $resultOrganisasi['B'] = $organisasi;
            } elseif ($organisasi >= 56 && $organisasi < 70) {
                $resultOrganisasi['C'] = $organisasi;
            } elseif ($organisasi >= 0 && $organisasi < 56) {
                $resultOrganisasi['K'] = $organisasi;
            }
        @endphp
        <tr>
            <td class="td-nilai">7</td>
            <td style="border: 1px solid #000000;"> Mampu Mengorganisasikan perkerjaan berdasarakan visi ke depan</td>
            <td class="td-nilai">{{ $resultOrganisasi['SB'] }}</td>
            <td class="td-nilai">{{ $resultOrganisasi['B'] }}</td>
            <td class="td-nilai">{{ $resultOrganisasi['C'] }}</td>
            <td class="td-nilai">{{ $resultOrganisasi['K'] }}</td>
        </tr>
    </table>
    <br>
    <table class="table-sign">
        <tr>
            <td>
                <div style="text-align: left">
                    <div style="display: inline-block; text-align: center">
                        Cimahi, <?php echo date('d F Y'); ?> <br>
                        Pembimbing
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <b><u>{{ $penilaian->nama_pembimbing }}</u></b><br>
                        NIK. {{ $penilaian->nik }}
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>

</html>

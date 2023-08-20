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

        #text-title2 {
            font-size: 17px;
        }

        .table-cell {
            width: 150px;
            white-space: nowrap;
        }

        .table-cell:after {
            content: "\00a0\00a0\00a0\00a0";
            /* Tambahkan empat karakter spasi */
        }

        .table-catatan-khusus {
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
        }

        .table-sign {
            border-collapse: collapse;
            float: right;
        }

        .td-catatan-khusus {
            border: 1px solid #000000;
            text-align: left;
            padding: 1px;
            height: 600px;
            vertical-align: top;
        }
    </style>
</head>

<body>
    <center>
        <h5 id="text-title">CATATAN KHUSUS PKL</h5>
        <p id="text-title2">(Presentasi, Permasalahan,Keunikan,dll)</p>
    </center>

    <table class="table-catatan-khusus">
        <tr>
            <td class="td-catatan-khusus"> {{ $catatan_khusus->catatan }}</td>
        </tr>
    </table>

    <table class="table-sign">
        <tr>
            @php
                setlocale(LC_TIME, 'id');
            @endphp
            <td style="text-align: left; padding-top: 10px;" colspan="2">Cimahi, <?php echo strftime('%e %B %Y'); ?></td>
        </tr>
        <tr>
         
                <div style="text-align: left">
                    Mahasiswa
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <b><u>{{ $catatan_khusus->nama }}</u></b><br>
                    NIM. {{ $catatan_khusus->nim }}
                </div>
           
        </tr>
    </table>
    
</body>

</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transkrip Nilai</title>
    <style>
        @page {
            size: F4 portrait;
            margin: 1cm;
        }

        body {
            font-family: 'Times New Roman', serif;
            font-size: 11px;
            background-color: #fbfaf5;
            margin: 0;
            position: relative;
            box-sizing: border-box;
            border: 2px solid #c5a880;
            min-height: 98vh;
            padding: 2% 3%;
        }

        /* Watermark */
        body::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 50%;
            height: 50%;
            background: url('{{ public_path("logo.png") }}') no-repeat center;
            background-size: contain;
            opacity: 0.08;
            transform: translate(-50%, -50%);
            z-index: 0;
        }

        body::before {
            content: "";
            position: absolute;
            top: 1%;
            left: 1%;
            right: 1%;
            bottom: 1%;
            border: 2px solid #c5a880;
            z-index: 2;
        }

        .ornament.top-left,
        .ornament.bottom-right {
            position: absolute;
            opacity: 0.08;
            z-index: -1;
            border-radius: 50%;
        }
        .ornament.top-left {
            width: 12vw; height: 12vw;
            background-color: #1a3d7c;
            top: -6vw; left: -6vw;
        }
        .ornament.bottom-right {
            width: 12vw; height: 12vw;
            background-color: #c5a880;
            bottom: -6vw; right: -6vw;
        }

        .logo {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 70px;
            height: auto;
            z-index: 2;
        }
        .nomor-surat {
            position: absolute;
            top: 25px;
            right: 20px;
            font-size: 12px;
            font-weight: bold;
            color: #333;
            z-index: 2;
        }

        .main-content {
            position: relative;
            z-index: 3;
            text-align: center;
            margin-top: 60px;
        }

        .main-content h2 {
            font-size: 20px;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }
        .main-content p {
            margin: 3px 0;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 11px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        td {
            vertical-align: top;
        }

        .footer {
            margin-top: 30px;
            font-size: 11px;
            text-align: right;
        }

        .signature-block {
            margin-top: 50px;
            text-align: right;
            font-size: 11px;
        }
        .signature-name {
            margin-top: 50px;
            font-weight: bold;
            border-top: 1px solid #000;
            display: inline-block;
            padding-top: 5px;
        }
        .signature-title {
            margin-top: 3px;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="ornament top-left"></div>
    <div class="ornament bottom-right"></div>

    <img src="{{ public_path('logo.png') }}" alt="Logo" class="logo">
    <div class="nomor-surat">No: {{ $nomor }}</div>

    <div class="main-content">
        <h2>Transkrip Nilai</h2>
        <p><strong>Nama:</strong> {{ $nama }}</p>
        <p><strong>Kompetensi:</strong> {{ $gelar }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:15%;">No. Elemen</th>
                <th style="width:65%;">Kompetensi</th>
                <th style="width:20%;">Status Kompetensi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                <td style="text-align:center">{{ $row['no_elemen_form_3'] }}</td>
                <td>{{ $row['nama_elemen'] }}</td>
                <td style="text-align:center">{{ $row['final'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
    </div>

    <div class="signature-block">
        <div class="signature-title" style="margin-bottom: 50px;">Asesor</div>
        <div class="signature-name">{{ $asesor_name ?? '___________________________' }}</div>
    </div>
</body>
</html>

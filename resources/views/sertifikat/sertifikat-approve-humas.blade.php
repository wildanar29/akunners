<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sertifikat Kompetensi Keperawatan</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 1cm;
        }

        body {
            font-family: 'Times New Roman', serif;
            text-align: center;
            background-color: #fbfaf5;
            margin: 0;
            position: relative;
            box-sizing: border-box;
            border: 2px solid #c5a880;
            min-height: 98vh;
            padding: 2% 3%;
        }

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
            border: 3px solid #c5a880;
            z-index: 2;
        }

        .main-content {
            position: relative;
            z-index: 3;
        }

        h1 {
            font-size: 42px;
            margin-bottom: 10px;
            letter-spacing: 2px;
        }

        h2 {
            font-size: 22px;
            margin-bottom: 30px;
        }

        .nama {
            font-size: 32px;
            font-weight: bold;
            margin: 20px 0;
            text-decoration: underline;
        }

        .description {
            font-size: 18px;
            width: 80%;
            margin: auto;
            line-height: 1.6;
        }

        .date-range {
            font-size: 18px;
            margin-top: 10px;
        }

        .status {
            margin-top: 30px;
            font-size: 28px;
            font-weight: bold;
        }

        .status.green { color: #006400; }
        .status.red { color: #e60000; }

        .gelar {
            margin-top: 30px;
            font-size: 18px;
        }

        .footer {
            margin-top: 80px;
            text-align: center;
            width: 100%;
            position: relative;
            z-index: 3;
        }

        .logo {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 100px;
        }

        .nomor-surat {
            position: absolute;
            top: 30px;
            right: 20px;
            font-size: 14px;
            font-weight: bold;
        }

        .qr {
            margin-bottom: 10px;
        }

        .ttd {
            border-top: 1px solid #000;
            width: 350px;
            margin: 10px auto 0 auto;
            padding-top: 6px;
            font-weight: bold;
        }

        .jabatan {
            font-size: 14px;
            margin-top: 4px;
        }

    </style>
</head>

<body>

    <img src="{{ public_path('logo.png') }}" class="logo">
    <div class="nomor-surat">No: {{ $nomor_surat ?? '-' }}</div>

    <div class="main-content">

        <h1>SERTIFIKAT<br>KOMPETENSI KEPERAWATAN</h1>
        <h2>Diberikan Kepada :</h2>

        <div class="nama">{{ $nama }}</div>

        <p class="description">
            Telah Mengikuti <strong>Asesmen Kompetensi Perawat</strong>
        </p>

        <p class="date-range">
            pada Tanggal <strong>{{ $tanggal_mulai ?? '-' }}</strong>
            s/d <strong>{{ $tanggal_selesai ?? '-' }}</strong>
            dan dinyatakan:
        </p>

        <div class="status {{ ($status ?? 'KOMPETEN') === 'BELUM KOMPETEN' ? 'red' : 'green' }}">
            {{ strtoupper($status ?? 'KOMPETEN') }}
        </div>

        <p class="gelar">
            Sebagai <strong>{{ $gelar ?? '-' }}</strong>
            di Area Keperawatan <strong>Rumah Sakit Immanuel</strong>
        </p>

    </div>

    <!-- FOOTER DIREKTUR DENGAN QR -->
    <div class="footer">

        {{-- QR CODE DIREKTUR --}}
        @if(!empty($barcode_direktur))
            <div class="qr">
                <img src="data:image/png;base64,{{ $barcode_direktur }}" width="120">
            </div>
        @endif

        <div class="ttd">
            dr. DAVID SANTOSO, M.M.
        </div>

        <div class="jabatan">
            Direktur Utama RS Immanuel
        </div>

    </div>

</body>
</html>

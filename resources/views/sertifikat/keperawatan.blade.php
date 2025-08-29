<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sertifikat Kompetensi Keperawatan</title>
    <style>
        /*
         * Responsive Certificate Styling
         * Using relative units for proportionality
         */
        @page {
            size: A4 landscape;
            margin: 1cm;
        }

        body {
            font-family: 'Times New Roman', serif;
            text-align: center;
            background-color: #fbfaf5; /* Cream background */
            margin: 0;
            position: relative;
            box-sizing: border-box;
            border: 2px solid #c5a880; /* Thinner border */
            min-height: 98vh;
            display: flex;
            flex-direction: column;
            justify-content: center; /* Centers content vertically */
            align-items: center; /* Centers content horizontally */
            padding: 2% 3%;
        }

        /* 👇 Watermark transparan */
        body::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 50%;
            height: 50%;
            background: url('{{ public_path("logo.png") }}') no-repeat center;
            background-size: contain;
            opacity: 0.1; /* lebih halus */
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
            z-index: 0;
        }

        /* Simplified Ornaments for a cleaner look */
        .ornament {
            position: absolute;
            opacity: 0.1;
            z-index: -1;
        }
        .ornament.top-left {
            width: 15vw;
            height: 15vw;
            background-color: #1a3d7c;
            border-radius: 50%;
            top: -7vw;
            left: -7vw;
        }
        .ornament.bottom-right {
            width: 15vw;
            height: 15vw;
            background-color: #c5a880;
            border-radius: 50%;
            bottom: -7vw;
            right: -7vw;
        }
        
        /* Main content container for perfect centering */
        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        /* Title and text styling */
        h1 {
            font-size: 4vw; /* Slightly larger */
            margin-bottom: 1vw;
            letter-spacing: 0.2vw;
            color: #2c2c2c;
        }

        h2 {
            font-size: 2.2vw; /* Slightly larger */
            margin-bottom: 2vw;
            color: #444;
        }

        .nama {
            font-size: 3.2vw; /* Slightly larger */
            font-weight: bold;
            margin: 1.5vw 0 2vw;
            text-decoration: underline;
        }

        .description {
            font-size: 1.8vw; /* Slightly larger */
            width: 80%;
            margin: 0 auto 1vw;
            line-height: 1.5;
        }

        .date-range {
            font-size: 1.8vw; /* Slightly larger */
            margin-top: 0.6vw;
        }

        .status {
            margin-top: 2.5vw;
            font-size: 3vw; /* Slightly larger */
            font-weight: bold;
            letter-spacing: 0.2vw;
            color: #006400;
        }

        .gelar {
            margin-top: 2.5vw;
            font-size: 1.8vw; /* Slightly larger */
        }

        /* Footer styling */
        .footer {
            margin-top: 6vw;
            text-align: center;
            width: 100%;
        }

        .signature-block {
            width: 40%;
            margin-left: auto;
            margin-right: auto;
        }
        
        .signature-image {
            width: 120px;   /* atur lebar sesuai kebutuhan */
            height: auto;   /* biar proporsional */
        }

        /* Space for signature */
        .signature-space {
            margin-top: 20px;
            text-align: center; /* supaya gambar ada di tengah */
        }

        .signature-name {
            font-weight: bold;
            font-size: 1.8vw; /* Slightly larger */
            border-top: 1px solid #000;
            padding-top: 0.6vw;
        }

        .signature-title {
            font-size: 1.5vw; /* Slightly larger */
            color: #333;
            margin-top: 0.4vw;
        }
    </style>
</head>
<body>
    <div class="ornament top-left"></div>
    <div class="ornament bottom-right"></div>
    
    <div class="main-content">
        <h1>SERTIFIKAT KOMPETENSI KEPERAWATAN</h1>
        <h2>Diberikan Kepada :</h2>
    
        <div class="nama">{{ $nama }}</div>
    
        <p class="description">
            Telah Mengikuti <strong>Asesmen Kompetensi Perawat</strong>
        </p>
        <p class="date-range">
            pada Tanggal <strong>{{ $tanggal_mulai ?? '-' }}</strong> s/d <strong>{{ $tanggal_selesai ?? '-' }}</strong> dan dinyatakan:
        </p>
    
        <div class="status">
            {{ strtoupper($status ?? 'KOMPETEN') }}
        </div>
    
        <p class="gelar">
            Sebagai <strong>Perawat Klinis 1 (Satu)</strong> di Area Keperawatan <strong>Rumah Sakit Immanuel</strong>
        </p>
    </div>

    <div class="footer">
        <div class="signature-block">
            <div class="signature-space">
                <img src="{{ public_path('ttd.png') }}" alt="Tanda Tangan" class="signature-image">
            </div>
            <div class="signature-name">dr. Danurrendra, Sp. B., CRP., CHAE.</div>
            <div class="signature-title">Direktur Utama RS Immanuel</div>
        </div>
    </div>
</body>
</html>
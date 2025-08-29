<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sertifikat Kompetensi Keperawatan</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        body {
            font-family: 'Times New Roman', serif;
            text-align: center;
            background-color: #fbfaf5;
            margin: 0;
            position: relative;
            box-sizing: border-box;
            border: 2px solid #c5a880;
            width: 100%;
            height: 100%;
            padding: 20px 30px;
            overflow: hidden;
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
            opacity: 0.08; /* lebih halus */
            transform: translate(-50%, -50%);
            z-index: 0;
        }

        /* Border dalam */
        body::before {
            content: "";
            position: absolute;
            top: 10px;
            left: 10px;
            right: 10px;
            bottom: 10px;
            border: 3px solid #c5a880;
            z-index: 1;
        }

        .main-content {
            position: relative;
            z-index: 2;
            width: 100%;
            padding: 40px 20px;
        }

        h1 { font-size: 32px; margin-bottom: 10px; letter-spacing: 2px; color: #2c2c2c; }
        h2 { font-size: 20px; margin-bottom: 20px; color: #444; }
        .nama { font-size: 26px; font-weight: bold; margin: 20px 0 30px; text-decoration: underline; }
        .description { font-size: 18px; width: 80%; margin: 0 auto 10px; line-height: 1.5; }
        .date-range { font-size: 18px; margin-top: 5px; }
        .status { margin-top: 30px; font-size: 26px; font-weight: bold; letter-spacing: 2px; color: #006400; }
        .gelar { margin-top: 25px; font-size: 18px; }

        .footer { margin-top: 60px; text-align: center; width: 100%; position: relative; z-index: 2; }
        .signature-block { width: 40%; margin-left: auto; margin-right: auto; }
        .signature-space { margin-top: 60px; }
        .signature-name { font-weight: bold; font-size: 18px; border-top: 1px solid #000; padding-top: 6px; }
        .signature-title { font-size: 15px; color: #333; margin-top: 4px; }
    </style>
</head>
<body>
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
            <div class="signature-space"></div>
            <div class="signature-name">dr. Danurrendra, Sp. B., CRP., CHAE.</div>
            <div class="signature-title">Direktur Utama RS Immanuel</div>
        </div>
    </div>
</body>
</html>

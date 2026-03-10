<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sertifikat Kompetensi Keperawatan</title>

    @php
        $logoPath = public_path('logo.png');
        $logoBase64 = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;
    @endphp

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
            background: url('{{ $logoBase64 }}') no-repeat center;
            background-size: contain;
            opacity: 0.1;
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

        .main-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        h1 {
            font-size: 4vw;
            margin-bottom: 1vw;
            letter-spacing: 0.2vw;
            color: #2c2c2c;
        }

        h2 {
            font-size: 2.2vw;
            margin-bottom: 2vw;
            color: #444;
            font-weight: normal;
        }

        .nama {
            font-size: 3.2vw;
            font-weight: normal;
            margin: 1.5vw 0 2vw;
            text-decoration: underline;
        }

        .description {
            font-size: 1.8vw;
            width: 80%;
            margin: 0 auto 1vw;
            line-height: 1.5;
            color: #333;
        }

        .date-range {
            font-size: 1.8vw;
            margin-top: 0.6vw;
            color: #333;
        }

        .status {
            margin-top: 2.5vw;
            font-size: 5vw;
            font-weight: normal;
            letter-spacing: 0.2vw;
            color: #000;
        }

        .gelar {
            margin-top: 2.5vw;
            font-size: 1.8vw;
            color: #333;
        }

        .footer {
            margin-top: 4vw;
            text-align: center;
            width: 100%;
        }

        .logo {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 100px;
            z-index: 2;
        }

        .nomor-surat {
            position: absolute;
            top: 30px;
            right: 20px;
            font-size: 14px;
            color: #333;
            z-index: 2;
        }

        .line {
            width: 20px;
            height: 100%;
        }

        .line.yellow { background:#B09B5C; opacity:0.3; }
        .line.red { background:#BA2822; opacity:0.3; }
        .line.blue { background:#153584; opacity:0.3; }

        .blue-lines {
            position:absolute;
            top:0;
            right:80px;
            height:100%;
            display:flex;
            gap:10px;
        }

        .yellow-lines {
            position:absolute;
            top:0;
            right:50px;
            height:100%;
            display:flex;
            gap:10px;
        }

        .red-lines {
            position:absolute;
            top:0;
            right:120px;
            height:100%;
            display:flex;
            gap:10px;
        }

    </style>
</head>

<body>

<div class="ornament top-left"></div>
<div class="ornament bottom-right"></div>

<img src="{{ $logoBase64 }}" class="logo">
<div class="nomor-surat">No: {{ $nomor_surat ?? '-' }}</div>

<div class="blue-lines"><div class="line blue"></div></div>
<div class="yellow-lines"><div class="line yellow"></div></div>
<div class="red-lines"><div class="line red"></div></div>

<div class="main-content">

    <h1>SERTIFIKAT<br>KOMPETENSI KEPERAWATAN</h1>

    <h2>Diberikan Kepada :</h2>

    <div class="nama">{{ $nama }}</div>

    <p class="description">
        Telah Mengikuti Asesmen Kompetensi Perawat
    </p>

    <p class="date-range">
        pada Tanggal <span>{{ $tanggal_mulai ?? '-' }}</span>
        s/d <span>{{ $tanggal_selesai ?? '-' }}</span>
        dan dinyatakan:
    </p>

    <div class="status">
        {{ strtoupper($status ?? 'KOMPETEN') }}
    </div>

    <p class="gelar">
        Sebagai <span>{{ $gelar ?? '-' }}</span>
        di Area Keperawatan <span>Rumah Sakit Immanuel</span>
    </p>

</div>

<!-- FOOTER DIREKTUR -->
<div class="footer">

    <table style="width:100%; text-align:center;">
        <tr>
            <td>

                @if(!empty($barcode_direktur))
                    <div style="padding-bottom:12px;">
                        <img src="data:image/png;base64,{{ $barcode_direktur }}" width="105">
                    </div>
                @endif

                <div style="
                    border-top:1px solid #000;
                    width:350px;
                    margin:15px auto 0 auto;
                    padding-top:6px;">
                    {{ $nama_direktur ?? 'dr. DAVID SANTOSO, M.M.' }}
                </div>

                <div style="font-size:14px; margin-top:4px;">
                    {{ $jabatan_direktur ?? 'Direktur Utama RS Immanuel' }}
                </div>

            </td>
        </tr>
    </table>

</div>

</body>
</html>
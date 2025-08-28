<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sertifikat Kompetensi</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            text-align: center;
            padding: 60px;
            background: #fbfaf5;
            border: 15px solid #c5a880;
            outline: 10px solid #000;
            margin: 0;
        }
        h1 {
            font-size: 52px;
            margin-bottom: 10px;
            letter-spacing: 6px;
            color: #2c2c2c;
        }
        h2 {
            font-size: 24px;
            font-weight: normal;
            margin-top: 0;
            margin-bottom: 40px;
            color: #444;
        }
        .nama {
            font-size: 38px;
            font-weight: bold;
            margin: 30px 0 15px 0;
            color: #1a1a1a;
            text-decoration: underline;
        }
        .deskripsi {
            font-size: 18px;
            color: #333;
            margin: 0 auto;
            width: 80%;
            line-height: 1.6;
        }
        .tanggal {
            margin-top: 50px;
            font-size: 18px;
            font-style: italic;
            color: #444;
        }
        .footer {
            margin-top: 100px;
            display: flex;
            justify-content: space-between;
            padding: 0 80px;
        }
        .ttd {
            font-size: 16px;
            border-top: 1px solid #000;
            width: 220px;
            margin: 0 auto;
            padding-top: 8px;
        }
    </style>
</head>
<body>
    <h1>SERTIFIKAT KOMPETENSI</h1>
    <h2>Diberikan kepada:</h2>

    <div class="nama">{{ $nama }}</div>

    <p class="deskripsi">
        Dengan ini menyatakan bahwa yang bersangkutan telah <br>
        menyelesaikan proses <strong>asesmen kompetensi</strong> sesuai standar yang ditetapkan, <br>
        dan dinyatakan memenuhi kriteria kelulusan pada bidang yang diujikan.
    </p>

    <div class="tanggal">{{ $tanggal }}</div>

    <div class="footer">
        <div>
            <div class="ttd">Asesor</div>
        </div>
        <div>
            <div class="ttd">Ketua LSP</div>
        </div>
    </div>
</body>
</html>

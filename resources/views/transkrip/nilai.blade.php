<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transkrip Nilai</title>

    @php
        $logoPath = public_path('logo.png');
        $logoBase64 = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;
    @endphp
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

        body::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 50%;
            height: 50%;
            background: url('{{ $logoBase64 }}') no-repeat center;
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

        .logo {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 70px;
            z-index: 2;
        }

        .nomor-surat {
            position: absolute;
            top: 25px;
            right: 20px;
            font-size: 12px;
            font-weight: bold;
            z-index: 2;
        }

        .main-content {
            text-align: center;
            margin-top: 60px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table.data-table th,
        table.data-table td {
            border: 1px solid #000;
            padding: 6px;
        }

        table.data-table th {
            background-color: #f0f0f0;
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
        }

        .signature-wrapper {
            width: 100%;
            margin-top: 70px;
            text-align: center;
        }

        .signature-column {
            width: 40%;
            display: inline-block;
            vertical-align: top;
        }

        .signature-left {
            text-align: left;
            margin-right: 5%;
        }

        .signature-right {
            text-align: right;
            margin-left: 5%;
        }

        .signature-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .signature-name {
            margin-top: 10px;
            font-weight: bold;
            border-top: 1px solid #000;
            display: inline-block;
            padding-top: 5px;
        }

        .signature-reg {
            font-size: 10px;
            margin-top: 3px;
        }

    </style>
</head>

<body>

    @if($logoBase64)
        <img src="{{ $logoBase64 }}" class="logo">
    @endif
    <div class="nomor-surat">No: {{ $nomor }}</div>

    <div class="main-content">
        <h2>Daftar Kompetensi</h2>
        <p><strong>Nama:</strong> {{ $nama }}</p>
        <p>{{ $nik_asesi ?? '-' }}</p>
        <p><strong>Level:</strong> {{ $gelar }}</p>
    </div>


    <table class="data-table">
        <thead> 
            <tr>
                <th style="width:20%;">NO ELEMEN</th>
                <th style="width:80%;">JUDUL ELEMEN KOMPETENSI</th>

                {{-- <th style="width:20%;">Status Kompetensi</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                <td style="text-align:center">{{ $row['no_elemen_form_3'] }}</td>
                <td>{{ $row['nama_elemen'] }}</td>

                {{-- <td style="text-align:center">{{ $row['final'] }}</td> --}}
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
    </div>

    <div class="signature-wrapper">

        <!-- BIDANG -->
        <div class="signature-column signature-left">

            <div class="signature-title">
                Bidang Keperawatan
            </div>

            @if(isset($barcode_bidang))
                <div>
                    <img src="data:image/png;base64,{{ $barcode_bidang }}" width="85">
                </div>
            @endif

            <div class="signature-name">
                {{ $bidang_name ?? 'BIDANG KEPERAWATAN' }}
            </div>

            <div class="signature-reg">
                No. Registrasi: {{ $bidang_reg ?? '-' }}
            </div>

        </div>

        <!-- ASESOR -->
        <div class="signature-column signature-right">

            <div class="signature-title">
                Asesor Kompetensi
            </div>

            @if(isset($barcode_asesor))
                <div>
                    <img src="data:image/png;base64,{{ $barcode_asesor }}" width="85">
                </div>
            @endif

            <div class="signature-name">
                {{ $asesor_name ?? '___________________' }}
            </div>

            <div class="signature-reg">
                No. Registrasi: {{ $asesor_reg ?? '-' }}
            </div>

        </div>

    </div>

</body>
</html>

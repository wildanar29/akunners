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
    size: A4 landscape;
    margin: 0.8cm;
}

body {
    font-family: 'Times New Roman', serif;
    font-size: 10px;
    line-height: 1.25;
    background-color: #fbfaf5;

    width: 29.7cm;
    height: 21cm;

    margin: 0;
    position: relative;
    box-sizing: border-box;
    border: 2px solid #c5a880;
    padding: 1.2% 2%;
}

/* WATERMARK */
body::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 45%;
    height: 45%;
    background: url('{{ $logoBase64 }}') no-repeat center;
    background-size: contain;
    opacity: 0.06;
    transform: translate(-50%, -50%);
    z-index: 0;
}

/* BORDER DALAM */
body::before {
    content: "";
    position: absolute;
    top: 0.7%;
    left: 0.7%;
    right: 0.7%;
    bottom: 0.7%;
    border: 2px solid #c5a880;
    z-index: 2;
}

/* LOGO */
.logo {
    position: absolute;
    top: 15px;
    left: 18px;
    width: 60px;
    z-index: 2;
}

/* NOMOR SURAT */
.nomor-surat {
    position: absolute;
    top: 18px;
    right: 18px;
    font-size: 10px;
    font-weight: bold;
    z-index: 2;
}

/* HEADER */
.main-content {
    text-align: center;
    margin-top: 40px;
}

.main-content h2 {
    margin-bottom: 4px;
    font-size: 14px;
}

.main-content p {
    margin: 2px 0;
}

/* TABLE */
table.data-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

table.data-table th,
table.data-table td {
    border: 1px solid #000;
    padding: 3px 5px;
}

table.data-table th {
    background-color: #f0f0f0;
    text-align: center;
    font-size: 10px;
}

table.data-table td {
    font-size: 10px;
}

/* FOOTER */
.footer {
    margin-top: 10px;
    text-align: right;
    font-size: 10px;
}

/* SIGNATURE */
.signature-wrapper {
    width: 100%;
    margin-top: 30px;
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
    font-size: 10px;
    font-weight: bold;
    margin-bottom: 4px;
}

.signature-name {
    margin-top: 6px;
    font-weight: bold;
    border-top: 1px solid #000;
    display: inline-block;
    padding-top: 3px;
    font-size: 10px;
}

.signature-reg {
    font-size: 10px;
    margin-top: 2px;
}

.qr-img {
    margin-top: 2px;
}

</style>
</head>

<body>

@if($logoBase64)
<img src="{{ $logoBase64 }}" class="logo">
@endif

<div class="nomor-surat">
No: {{ $nomor }}
</div>

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
</tr>
</thead>

<tbody>

@foreach($data as $row)
<tr>
<td style="text-align:center">
{{ $row['no_elemen_form_3'] }}
</td>

<td>
{{ $row['nama_elemen'] }}
</td>
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
<div class="qr-img">
<img src="data:image/png;base64,{{ $barcode_bidang }}" width="70">
</div>
@endif

<div class="signature-name">
{{ $bidang_name ?? 'BIDANG KEPERAWATAN' }}
</div>

<div class="signature-reg">
NIP: {{ $bidang_reg ?? '-' }}
</div>

</div>

<!-- ASESOR -->
<div class="signature-column signature-right">

<div class="signature-title">
Asesor Kompetensi
</div>

@if(isset($barcode_asesor))
<div class="qr-img">
<img src="data:image/png;base64,{{ $barcode_asesor }}" width="70">
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
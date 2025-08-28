<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf; // alias dari dompdf
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function generate($nama)
    {
        $data = [
            'nama' => ucfirst($nama),
            'tanggal' => date('d F Y'),
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('sertifikat', $data);

        // Pakai response() supaya header jelas
        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "inline; filename=sertifikat-{$nama}.pdf");
    }

}

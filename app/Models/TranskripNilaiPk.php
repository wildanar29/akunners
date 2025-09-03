<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TranskripNilaiPk extends Model
{
    protected $table = 'transkrip_nilai_pk';

    protected $fillable = [
        'asesi_id',
        'pk_id',
        'form_1_id',
        'nomor_urut',
        'nomor_dokumen',
        'nama',
        'gelar',
        'status',
        'tanggal_mulai',
        'tanggal_selesai',
        'file_path',
    ];

    /**
     * Relasi ke tabel Asesi (jika ada model Asesi).
     */
    public function asesi()
    {
        return $this->belongsTo(Asesi::class, 'asesi_id');
    }

    /**
     * Relasi ke tabel PK (jika ada model KompetensiPk atau PK).
     */
    public function pk()
    {
        return $this->belongsTo(KompetensiPk::class, 'pk_id');
    }

    /**
     * Relasi ke form_1 (jika ada model Form1).
     */
    public function form1()
    {
        return $this->belongsTo(Form1::class, 'form_1_id');
    }

    /**
     * Generate nomor dokumen otomatis + nomor urut.
     * Contoh format: 001/TNP/IX/2025
     */
    public static function generateNomorDokumen()
    {
        $bulanRomawi = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
            5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
            9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];

        $tahun = date('Y');
        $bulan = $bulanRomawi[date('n')];

        // ambil nomor urut terakhir dari tahun berjalan
        $last = self::whereYear('created_at', $tahun)
            ->orderBy('nomor_urut', 'desc')
            ->first();

        $nomorUrut = $last ? $last->nomor_urut + 1 : 1;
        $nomorUrutStr = str_pad($nomorUrut, 3, '0', STR_PAD_LEFT);

        // format nomor dokumen
        $nomorDokumen = "{$nomorUrutStr}/TNP/{$bulan}/{$tahun}";

        return [$nomorUrut, $nomorDokumen];
    }
}

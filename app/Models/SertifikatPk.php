<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SertifikatPk extends Model
{
    use HasFactory;

    protected $table = 'sertifikat_pk'; // nama tabel sesuai SQL

    protected $primaryKey = 'id';

    protected $fillable = [
        'asesi_id',
        'pk_id',
        'form_1_id',      // 👈 tambahkan field form_1_id
        'nomor_urut',
        'nomor_surat',
        'nama',
        'gelar',
        'status',
        'tanggal_mulai',
        'tanggal_selesai',
        'file_path',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
    ];

    public static function generateNomorSurat()
    {
        $bulan = date('n'); 
        $tahun = date('Y');

        $last = self::whereYear('created_at', $tahun)
                    ->whereMonth('created_at', $bulan)
                    ->max('nomor_urut');

        $next = $last ? $last + 1 : 1;

        $romawi = [1=>'I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];

        $nomorSurat = sprintf("%03d/ASSKOM-RSI/%s/%s", $next, $romawi[$bulan], $tahun);

        return [$next, $nomorSurat];
    }

    // Relasi ke tabel Asesi
    public function asesi()
    {
        return $this->belongsTo(Asesi::class, 'asesi_id');
    }

    // Relasi ke tabel PK
    public function pk()
    {
        return $this->belongsTo(Pk::class, 'pk_id');
    }

    // Relasi ke tabel Form1
    public function form1()
    {
        return $this->belongsTo(Form1::class, 'form_1_id');
    }
}

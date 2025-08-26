<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JawabanDaftarTilik extends Model
{
    protected $table = 'jawaban_daftar_tilik';
    protected $primaryKey = 'id';
    public $timestamps = true; // karena ada created_at & updated_at

    protected $fillable = [
        'form_10_id',
        'daftar_tilik_id',
        'kegiatan_daftar_tilik_id',
        'asesi_id',
        'asesor_id',
        'dilakukan',
        'catatan'
    ];

    // Relasi ke Form 10
    public function form10()
    {
        return $this->belongsTo(Form10::class, 'form_10_id', 'form_10_id');
    }

    // Relasi ke Daftar Tilik
    public function daftarTilik()
    {
        return $this->belongsTo(DaftarTilik::class, 'daftar_tilik_id', 'id');
    }

    // Relasi ke Kegiatan Daftar Tilik
    public function kegiatanDaftarTilik()
    {
        return $this->belongsTo(KegiatanDaftarTilik::class, 'kegiatan_daftar_tilik_id', 'id');
    }
}

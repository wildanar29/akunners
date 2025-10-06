<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanDaftarTilik extends Model
{
    protected $table = 'kegiatan_daftar_tilik';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'pk_id',
        'daftar_tilik_id',
        'parent_id',
        'kegiatan',
        'urutan',
        'isTitle',
        'dilakukan',
    ];

    // Relasi self-referential untuk parent kegiatan
    public function parent()
    {
        return $this->belongsTo(KegiatanDaftarTilik::class, 'parent_id');
    }

    // Relasi self-referential untuk child kegiatan
    public function children()
    {
        return $this->hasMany(KegiatanDaftarTilik::class, 'parent_id')
                    ->with('children'); // rekursif tapi tanpa jawaban
    }

    // Relasi ke daftar_tilik
    public function daftarTilik()
    {
        return $this->belongsTo(DaftarTilik::class, 'daftar_tilik_id');
    }

    public function jawaban()
    {
        return $this->hasMany(JawabanDaftarTilik::class, 'kegiatan_daftar_tilik_id', 'id');
    }
    
}

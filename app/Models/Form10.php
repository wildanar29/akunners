<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form10 extends Model
{
    // Nama tabel
    protected $table = 'form_10';

    // Primary key
    protected $primaryKey = 'form_10_id';

    // Laravel timestamps sudah aktif, jadi tidak perlu override
    public $timestamps = true;

    // Kolom yang boleh diisi
    protected $fillable = [
        'pk_id',
        'daftar_tilik_id',
        'asesi_id',
        'asesi_name',
        'asesi_date',
        'asesor_id',
        'asesor_name',
        'asesor_date',
        'no_reg',
        'status',
        'ket',
    ];

    // Relasi ke tabel daftar_tilik
    public function daftarTilik()
    {
        return $this->belongsTo(DaftarTilik::class, 'daftar_tilik_id');
    }

    // Relasi ke model asesi (jika ada)
    public function asesi()
    {
        return $this->belongsTo(User::class, 'asesi_id'); // atau ganti dengan model Asesi jika ada
    }

    // Relasi ke model asesor (jika ada)
    public function asesor()
    {
        return $this->belongsTo(User::class, 'asesor_id'); // atau ganti dengan model Asesor jika ada
    }
}

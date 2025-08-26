<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DaftarTilik extends Model
{
    // Nama tabel jika berbeda dari plural model
    protected $table = 'daftar_tilik';

    // Primary key default adalah 'id', jadi ini opsional
    protected $primaryKey = 'id';

    // Jika kamu tidak menggunakan timestamp otomatis dari Laravel, atur false
    public $timestamps = true;

    // Jika kolom created_at dan updated_at bukan menggunakan tipe timestamp standar, kamu bisa custom formatnya,
    // tapi default sudah sesuai dengan definisimu, jadi tidak perlu override.

    // Kolom yang boleh diisi (mass assignable)
    protected $fillable = [
        'pk_id',
        'form_number',
        'urutan',
        'description',
    ];

    public function kegiatanDaftarTilik()
    {
        return $this->hasMany(KegiatanDaftarTilik::class, 'daftar_tilik_id');
    }

    public function kegiatanDaftarTilikDenganJawaban()
    {
        return $this->hasMany(KegiatanDaftarTilik::class, 'daftar_tilik_id')
                    ->with('jawaban');
    }
}

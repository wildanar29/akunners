<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoinForm6 extends Model
{
    // Nama tabel di database
    protected $table = 'poin_form6';

    // Primary key dari tabel
    protected $primaryKey = 'id';

    // Menggunakan timestamps (created_at & updated_at)
    public $timestamps = true;

    // Kolom yang bisa diisi secara massal
    protected $fillable = [
        'kegiatan_id',
        'parent_id',
        'isi_poin',
        'urutan',
    ];

    // Casting (jika diperlukan)
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}

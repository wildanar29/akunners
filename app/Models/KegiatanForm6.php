<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanForm6 extends Model
{
    // Nama tabel
    protected $table = 'kegiatan_form6';

    // Primary key
    protected $primaryKey = 'id';

    // Gunakan timestamps
    public $timestamps = true;

    // Kolom yang dapat diisi
    protected $fillable = [
        'pk_id',
        'langkah_id',
        'deskripsi',
        'catatan',
        'pencapaian',
    ];

    // Casting kolom ke tipe data yang sesuai
    protected $casts = [
        'pencapaian' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}

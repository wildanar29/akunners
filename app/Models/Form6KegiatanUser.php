<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form6KegiatanUser extends Model
{
    // Nama tabel yang digunakan
    protected $table = 'form6_kegiatan_user';

    // Primary key
    protected $primaryKey = 'id';

    // Timestamps aktif (created_at, updated_at)
    public $timestamps = true;

    // Kolom yang dapat diisi secara massal
    protected $fillable = [
        'form_6_id',
        'kegiatan_id',
        'is_tercapai',
        'catatan',
    ];

    // Cast tipe data otomatis
    protected $casts = [
        'is_tercapai' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}

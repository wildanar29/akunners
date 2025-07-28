<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LangkahForm6 extends Model
{
    // Nama tabel di database
    protected $table = 'langkah_form6';

    // Primary key tabel
    protected $primaryKey = 'id';

    // Gunakan timestamps (created_at dan updated_at)
    public $timestamps = true;

    // Kolom yang dapat diisi secara massal
    protected $fillable = [
        'pk_id',
        'nomor_langkah',
        'judul_langkah',
        'form_parent',
        'catatan',
    ];

    // Casting kolom (jika diperlukan)
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}

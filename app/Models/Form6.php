<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form6 extends Model
{
    // Nama tabel di database
    protected $table = 'form_6';

    // Primary key tabel
    protected $primaryKey = 'form_6_id';

    // Aktifkan timestamps jika kolom created_at dan updated_at tersedia
    public $timestamps = true;

    // Kolom yang bisa diisi secara massal
    protected $fillable = [
        'pk_id',
        'asesi_id',
        'asesi_name',
        'asesi_date',
        'asesor_id',
        'asesor_name',
        'asesor_date',
        'no_reg',
        'status',
    ];

    // Cast kolom tertentu ke tipe data tertentu
    protected $casts = [
        'asesi_date' => 'date',
        'asesor_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}

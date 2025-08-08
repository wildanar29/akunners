<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form4a extends Model
{
    // Nama tabel
    protected $table = 'form_4a';

    // Primary key
    protected $primaryKey = 'form_4a_id';

    // Tipe auto-increment (jika menggunakan UUID, set ke false)
    public $incrementing = true;

    // Tipe primary key
    protected $keyType = 'int';

    // Mengizinkan penggunaan timestamps
    public $timestamps = true;

    // Kolom yang bisa diisi (mass assignable)
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
        'ket',
    ];
}

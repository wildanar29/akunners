<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JawabanForm4b extends Model
{
    // Nama tabel
    protected $table = 'jawaban_form4b';

    // Primary key
    protected $primaryKey = 'id';

    // Kolom yang bisa diisi secara massal
    protected $fillable = [
        'form_1_id',
        'user_id',
        'iuk_form3_id',
        'jawaban_asesi',
        'pencapaian',
        'nilai',
        'catatan',
    ];

    // Mengaktifkan timestamp otomatis
    public $timestamps = true;
}

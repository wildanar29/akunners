<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form2 extends Model
{
    // Nama tabel
    protected $table = 'form_2';

    // Primary key
    protected $primaryKey = 'form_2_id';

    // Kolom yang bisa diisi
    protected $fillable = [
        'user_jawab_form_2_id',
        'penilaian_asesi',
        'asesi_date',
        'asesor_date',
        'no_reg',
        'asesi_name',
        'asesor_name',
        'status',
    ];

    // Timestamps otomatis (created_at & updated_at)
    public $timestamps = true;

    /**
     * Relasi ke JawabanForm2
     */
    public function jawabanForm2()
    {
        return $this->belongsTo(JawabanForm2::class, 'user_jawab_form_2_id', 'user_jawab_form_2_id');
    }
}

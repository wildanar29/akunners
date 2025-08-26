<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoalForm7 extends Model
{
    // Nama tabel
    protected $table = 'soal_form7';

    // Primary key
    protected $primaryKey = 'id';

    // Kolom yang bisa diisi mass assignment
    protected $fillable = [
        'pk_id',
        'iuk_form3_id',
        'pertanyaan',
        'sumber_form',
    ];

    // Laravel/Lumen otomatis handle created_at dan updated_at
    // tapi di tabel ini tidak ada, jadi kita nonaktifkan
    public $timestamps = false;

    /**
     * Relasi ke tabel iuk_form3
     */
    public function iukForm3()
    {
        return $this->belongsTo(IukModel::class, 'iuk_form3_id', 'iuk_form3_id');
    }

    public function jawabanForm7()
    {
        return $this->hasMany(JawabanForm7::class, 'soal_form7_id');
    }

}

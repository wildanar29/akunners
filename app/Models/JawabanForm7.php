<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JawabanForm7 extends Model
{
    protected $table = 'jawaban_form7'; // Nama tabel
    protected $primaryKey = 'id';       // Primary key
    public $timestamps = false;         // Karena tabel tidak punya created_at & updated_at

    protected $fillable = [
        'asesi_id',
        'asesor_id',
        'soal_form7_id',
        'keputusan',
        'catatan'
    ];

    /**
     * Relasi ke Soal Form 7
     */
    public function soalForm7()
    {
        return $this->belongsTo(SoalForm7::class, 'soal_form7_id', 'id');
    }

    /**
     * Relasi ke model User untuk asesi
     */
    public function asesi()
    {
        return $this->belongsTo(User::class, 'asesi_id', 'id');
    }

    /**
     * Relasi ke model User untuk asesor
     */
    public function asesor()
    {
        return $this->belongsTo(User::class, 'asesor_id', 'id');
    }
}

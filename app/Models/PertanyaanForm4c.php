<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PertanyaanForm4c extends Model
{
    protected $table = 'pertanyaan_form4c';

    protected $fillable = [
        'iuk_form_3_id',
        'parent_id',
        'pertanyaan',
        'urutan',
        'question_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi ke pertanyaan induk (jika ini adalah sub-pertanyaan/kasus)
    public function parent()
    {
        return $this->belongsTo(PertanyaanForm4c::class, 'parent_id');
    }

    // Relasi ke pertanyaan anak (jika ini adalah pertanyaan utama)
    public function children()
    {
        return $this->hasMany(PertanyaanForm4c::class, 'parent_id');
    }

    // Relasi ke tabel questions
    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function iuk()
    {
        return $this->belongsTo(\App\Models\IukModel::class, 'iuk_form_3_id', 'iuk_form3_id');
    }
}

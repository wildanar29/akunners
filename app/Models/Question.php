<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    // Nama tabel
    protected $table = 'questions';

    // Kolom yang bisa diisi (fillable)
    protected $fillable = [
        'question_text',
    ];

    // Casting untuk timestamp agar otomatis jadi objek Carbon
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi ke tabel pivot question_choice
    public function questionChoices()
    {
        return $this->hasMany(QuestionChoice::class, 'question_id');
    }

    // Relasi ke pertanyaan_form4c jika digunakan
    public function form4c()
    {
        return $this->hasMany(PertanyaanForm4c::class, 'question_id');
    }

    // Relasi ke choices melalui pivot
    public function choices()
    {
        return $this->belongsToMany(Choice::class, 'question_choice', 'question_id', 'choice_id')
                    ->withPivot('is_correct');
    }
}

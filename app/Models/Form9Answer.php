<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form9Answer extends Model
{
    protected $table = 'form9_answers';
    protected $primaryKey = 'answer_id';

    // Kolom yang boleh diisi (mass assignable)
    protected $fillable = [
        'form_9_id',
        'question_id',
        'sub_question_id',
        'user_id',
        'answer_text',
        'is_checked',
        'notes',
    ];

    // Relasi ke form_9
    public function form()
    {
        return $this->belongsTo(Form9::class, 'form_9_id', 'form_9_id');
    }

    // Relasi ke pertanyaan utama
    public function question()
    {
        return $this->belongsTo(Form9Question::class, 'question_id', 'question_id');
    }

    // Relasi ke sub pertanyaan (jika ada)
    public function subQuestion()
    {
        return $this->belongsTo(Form9SubQuestion::class, 'sub_question_id', 'sub_question_id');
    }

    // Relasi ke user (asesi / asesor)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}

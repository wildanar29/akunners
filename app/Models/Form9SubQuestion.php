<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form9SubQuestion extends Model
{
    protected $table = 'form9_sub_questions';
    protected $primaryKey = 'sub_question_id';
    public $timestamps = false; // karena tabel ini tidak punya created_at & updated_at

    // Kolom yang bisa diisi
    protected $fillable = [
        'question_id',
        'sub_label',
        'order_no',
    ];

    // Relasi ke pertanyaan utama
    public function question()
    {
        return $this->belongsTo(Form9Question::class, 'question_id', 'question_id');
    }

    // Relasi ke jawaban (form9_answers)
    public function answers()
    {
        return $this->hasMany(Form9Answer::class, 'sub_question_id', 'sub_question_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JawabanForm4c extends Model
{
    protected $table = 'jawaban_form4c';

    protected $fillable = [
        'form_1_id',
        'user_id',
        'pertanyaan_form4c_id',
        'question_choice_id',
        'catatan',
        'choice_label',
        'is_correct',
    ];


    // Relasi ke Form1
    public function form1()
    {
        return $this->belongsTo(Form1::class, 'form_1_id', 'form_1_id');
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Relasi ke PertanyaanForm4c
    public function pertanyaan()
    {
        return $this->belongsTo(PertanyaanForm4c::class, 'pertanyaan_form4c_id');
    }

    // Relasi ke QuestionChoice
    public function choice()
    {
        return $this->belongsTo(QuestionChoice::class, 'question_choice_id');
    }
}

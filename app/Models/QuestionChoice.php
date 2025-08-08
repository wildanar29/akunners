<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionChoice extends Model
{
    protected $table = 'question_choice';

    protected $fillable = [
        'question_id',
        'choice_id',
        'is_correct',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    // Relasi ke model Question
    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    // Relasi ke model Choice
    public function choice()
    {
        return $this->belongsTo(Choice::class, 'choice_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Choice extends Model
{
    protected $table = 'choices';

    protected $fillable = [
        'choice_label',
        'choice_text',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi ke question_choice
    public function questionChoices()
    {
        return $this->hasMany(QuestionChoice::class, 'choice_id');
    }

    // Relasi ke questions melalui tabel pivot question_choice
    public function questions()
    {
        return $this->belongsToMany(Question::class, 'question_choice', 'choice_id', 'question_id')
                    ->withPivot('is_correct');
    }
}

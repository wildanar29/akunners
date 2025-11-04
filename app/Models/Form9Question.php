<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form9Question extends Model
{
    use HasFactory;

    // Nama tabel
    protected $table = 'form9_questions';

    // Primary Key
    protected $primaryKey = 'question_id';

    protected $casts = [
        'has_sub_questions' => 'boolean', // ✅ otomatis ubah 0/1 → true/false
    ];
    
    // Mass assignment (fillable)
    protected $fillable = [
        'section',
        'sub_section',
        'question_text',
        'criteria',
        'order_no',
    ];

    // Kalau tidak pakai timestamps (created_at, updated_at)
    public $timestamps = false;

    // Relasi ke jawaban (kalau sudah ada tabel form9_answers)
    public function answers()
    {
        return $this->hasMany(Form9Answer::class, 'question_id', 'question_id');
    }

    // ✅ Relasi ke sub pertanyaan
    public function subQuestions()
    {
        return $this->hasMany(Form9SubQuestion::class, 'question_id', 'question_id');
    }
}

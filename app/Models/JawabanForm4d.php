<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JawabanForm4d extends Model
{
    protected $table = 'jawaban_form4d';

    protected $fillable = [
        'form_1_id',
        'user_id',
        'pertanyaan_form4d_id',
        'pencapaian',
    ];

    protected $casts = [
        'pencapaian' => 'boolean',
    ];

    /**
     * Relasi ke Form1
     */
    public function form1(): BelongsTo
    {
        return $this->belongsTo(Form1::class, 'form_1_id', 'form_1_id');
    }

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Relasi ke PertanyaanForm4d
     */
    public function pertanyaan(): BelongsTo
    {
        return $this->belongsTo(PertanyaanForm4d::class, 'pertanyaan_form4d_id');
    }
}

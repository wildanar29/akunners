<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JawabanForm6 extends Model
{
    use HasFactory;

    protected $table = 'jawaban_form6';

    protected $primaryKey = 'id';

    protected $fillable = [
        'pk_id',
        'kegiatan_id',
        'user_id',
        'pencapaian',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'pencapaian' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi ke kegiatan_form6
    public function kegiatan()
    {
        return $this->belongsTo(KegiatanForm6::class, 'kegiatan_id');
    }

    // Relasi ke user (jika kamu memiliki model User)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

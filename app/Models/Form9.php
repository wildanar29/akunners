<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form9 extends Model
{
    use HasFactory;

    // Nama tabel
    protected $table = 'form_9';

    // Primary Key
    protected $primaryKey = 'form_9_id';

    // Aktifkan timestamps (karena ada created_at dan updated_at)
    public $timestamps = true;

    // Kolom yang bisa diisi (mass assignment)
    protected $fillable = [
        'pk_id',
        'asesi_id',
        'asesi_name',
        'asesi_date',
        'asesor_id',
        'asesor_name',
        'asesor_date',
        'no_reg',
        'status',
    ];

    /**
     * Relasi ke user (asesi)
     */
    public function asesi()
    {
        return $this->belongsTo(User::class, 'asesi_id', 'user_id');
    }

    /**
     * Relasi ke user (asesor)
     */
    public function asesor()
    {
        return $this->belongsTo(User::class, 'asesor_id', 'user_id');
    }

    /**
     * Relasi ke jawaban form 9 (kalau sudah ada tabel form9_answers)
     */
    public function answers()
    {
        return $this->hasMany(Form9Answer::class, 'form_9_id', 'form_9_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Form8 extends Model
{
    use HasFactory;

    // Nama tabel
    protected $table = 'form_8';

    // Primary key
    protected $primaryKey = 'form_8_id';

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

    // Tipe data yang perlu casting
    protected $casts = [
        'asesi_date'  => 'date',
        'asesor_date' => 'date',
    ];

    // Relasi ke user (asesi)
    public function asesi()
    {
        return $this->belongsTo(User::class, 'asesi_id', 'user_id');
    }

    // Relasi ke user (asesor)
    public function asesor()
    {
        return $this->belongsTo(User::class, 'asesor_id', 'user_id');
    }
}

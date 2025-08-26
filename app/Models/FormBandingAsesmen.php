<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FormBandingAsesmen extends Model
{
    use HasFactory;

    // Nama tabel
    protected $table = 'form_banding_asesmen';

    // Primary key
    protected $primaryKey = 'banding_id';

    // Kolom yang bisa diisi (mass assignment)
    protected $fillable = [
        'asesi_id',
        'form_1_id',
        'asesor_id',
        'tanggal_asesmen',
        'alasan_banding',
        'persetujuan_asesi',
        'persetujuan_asesor',
    ];

    // Casting tipe data
    protected $casts = [
        'tanggal_asesmen'   => 'date',
        'persetujuan_asesi' => 'boolean',
        'persetujuan_asesor'=> 'boolean',
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

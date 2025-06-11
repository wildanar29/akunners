<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeForm3_B extends Model
{
    use HasFactory;

    protected $table = 'form3_b'; // Nama tabel di database
    protected $primaryKey = 'id'; // Primary key
    public $timestamps = false; // Jika tidak menggunakan created_at dan updated_at

    protected $fillable = [
        'no_iuk',
        'no_soal',
        'pertanyaan',
        'indikator_pencapaian'
    ];

    // Relasi ke tabel iuk_form3 berdasarkan no_iuk
    public function iukForm3()
    {
        return $this->belongsTo(IukModel::class, 'no_iuk', 'no_iuk');
    }



    
}

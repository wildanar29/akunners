<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IukModel extends Model
{
    use HasFactory;

    protected $table = 'iuk_form3'; // Nama tabel di database
    protected $primaryKey = 'iuk_form3_id'; // Primary key
    public $timestamps = false; // Jika tidak menggunakan created_at dan updated_at

    protected $fillable = [
        'no_kuk',
        'no_iuk',
        'iuk_name'
    ];

    // Relasi dengan KukModel berdasarkan no_kuk
    public function kukForm3()
    {
        return $this->belongsTo(KukModel::class, 'no_kuk', 'no_kuk');
    }

    // Relasi ke tabel form3_abcd berdasarkan no_iuk
    public function form3Abcd()
    {
        return $this->hasMany(TypeForm3_B::class, 'no_iuk', 'no_iuk');
    }

    
}

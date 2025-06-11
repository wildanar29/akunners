<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElemenForm3 extends Model
{
    use HasFactory;

    protected $table = 'elemen_form_3'; // Nama tabel di database
    protected $primaryKey = 'id'; // Primary key
    public $timestamps = false; // Jika tidak menggunakan created_at dan updated_at

    protected $fillable = [
        'no_elemen_form_3',
        'isi_elemen',
    ];

    // Relasi dengan tabel kuk_form3 berdasarkan no_elemen_form_3
    public function kukForm3()
    {
        return $this->hasMany(KukModel::class, 'no_elemen_form_3', 'no_elemen_form_3');
    }

    
}

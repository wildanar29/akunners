<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KukModel extends Model
{
    use HasFactory;

    protected $table = 'kuk_form3'; // Nama tabel di database
    protected $primaryKey = 'kuk_form3_id'; // Primary key
    public $timestamps = false; // Jika tidak menggunakan created_at dan updated_at

    protected $fillable = [
        'no_elemen_form_3',
        'isi_elemen',
        'no_kuk',
        'kuk_name',
    ];

     // Relasi dengan ElemenForm3 berdasarkan no_elemen_form_3
     public function elemenForm3()
     {
         return $this->belongsTo(ElemenForm3::class, 'no_elemen_form_3', 'no_elemen_form_3');
     }
 
     // Relasi dengan IukModel berdasarkan no_kuk
     public function iukForm3()
     {
         return $this->hasMany(IukModel::class, 'no_kuk', 'no_kuk');
     }

     
}

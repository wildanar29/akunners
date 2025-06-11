<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeForm3_D extends Model
{
    use HasFactory;

    protected $table = 'form3_d'; // Nama tabel di database
    protected $primaryKey = 'id'; // Primary key
    public $timestamps = false; // Jika tidak menggunakan created_at dan updated_at

    protected $fillable = [
        'no_kuk',
        'doc_id',
    ];

     // Relasi dengan tabel KukModel untuk mendapatkan kuk_name
     public function kukForm3()
     {
         return $this->belongsTo(KukModel::class, 'no_kuk', 'no_kuk');
     }
 
     // Relasi dengan tabel dokumen untuk mendapatkan nama_doc
     public function document()
     {
         return $this->belongsTo(DocKukForm3::class, 'doc_id', 'doc_id');
     }
    
}

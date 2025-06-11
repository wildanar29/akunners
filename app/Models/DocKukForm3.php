<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocKukForm3 extends Model
{
    use HasFactory;

    protected $table = 'doc_kuk_form3'; // Nama tabel di database
    protected $primaryKey = 'doc_id'; // Primary key
    public $timestamps = false; // Jika tidak menggunakan created_at dan updated_at

    protected $fillable = [
        'nama_doc',
    ];


    
}

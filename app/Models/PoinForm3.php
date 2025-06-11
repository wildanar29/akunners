<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoinForm3 extends Model
{
    use HasFactory;

    protected $table = 'poin_tabel_form3'; // Nama tabel di database
    protected $primaryKey = 'id'; // Primary key
    public $timestamps = false; // Jika tidak menggunakan created_at dan updated_at

    protected $fillable = [
        'no_poin',
        'poin_diamati',
    ];


    

    
}

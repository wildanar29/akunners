<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeForm3_A extends Model
{
    use HasFactory;

    protected $table = 'form3_a'; // Nama tabel di database
    protected $primaryKey = 'id'; // Primary key
    public $timestamps = false; // Jika tidak menggunakan created_at dan updated_at

    protected $fillable = [
        'no_iuk',
        'no_poin',
        'poin_diamati'
    ];


    // TypeForm3_A.php
    public function iukForm3()
    {
        return $this->belongsTo(IukModel::class, 'no_iuk', 'no_iuk');
    }

    public function poinForm3()
    {
        return $this->hasMany(PoinForm3::class, 'no_poin', 'no_poin');
    }



    
}

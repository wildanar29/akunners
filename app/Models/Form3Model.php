<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form3Model extends Model
{
    use HasFactory;

    protected $table = 'form_3'; // Nama tabel

    protected $primaryKey = 'form_3_id'; // Primary Key

    protected $fillable = [
        'user_id',
        'asesi_date',
        'asesor_date',
        'no_reg',
        'asesi_name',
        'asesor_name',
        'status',
    ];

}

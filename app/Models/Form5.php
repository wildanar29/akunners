<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form5 extends Model
{
    protected $table = 'form_5'; // nama tabel di database

    protected $primaryKey = 'form_5_id'; // primary key

    public $timestamps = true; // untuk created_at dan updated_at

    protected $fillable = [
        'asesi_id',
        'asesi_name',
        'asesi_date',
        'asesor_id',
        'asesor_name',
        'asesor_date',
        'no_reg',
        'status'
    ];

    protected $casts = [
        'asesi_date' => 'date',
        'asesor_date' => 'date',
    ];
}

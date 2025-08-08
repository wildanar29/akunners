<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form4c extends Model
{
    protected $table = 'form_4c';
    protected $primaryKey = 'form_4c_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'pk_id',
        'asesi_id',
        'asesi_name',
        'asesi_date',
        'asesor_id',
        'asesor_name',
        'asesor_date',
        'no_reg',
        'status',
        'ket',
    ];
}

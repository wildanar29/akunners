<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianForm2Model extends Model
{
    use HasFactory;

    protected $table = 'form_2'; // Nama tabel

    protected $primaryKey = 'form_2_id'; // Primary Key

    protected $fillable = [
        'user_jawab_form_2_id',
        'penilaian_asesi',
        'penilaian_asesor',
        'asesi_date',
        'asesor_date',
        'no_reg',
        'asesi_name',
        'asesor_name',
        'status'
    ];

}

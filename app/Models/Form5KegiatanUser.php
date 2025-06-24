<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form5KegiatanUser extends Model
{
    protected $table = 'form5_kegiatan_user';

    protected $fillable = [
        'form_5_id',
        'kegiatan_id',
        'is_tercapai',
        'catatan',
        'created_at',
        'updated_at'
    ];

    public $timestamps = true;
}

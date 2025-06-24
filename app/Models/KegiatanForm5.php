<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanForm5 extends Model
{
    protected $table = 'kegiatan_form5';
    protected $fillable = ['langkah_id', 'deskripsi', 'is_tercapai', 'catatan'];
}


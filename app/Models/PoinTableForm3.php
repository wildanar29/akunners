<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoinTableForm3 extends Model
{
    use HasFactory;

    protected $table = 'poin_tabel_form3'; // Pastikan ini sesuai dengan nama tabel di database

    public function elemenForm3()
    {
        return $this->belongsTo(ElemenForm3::class, 'elemen_form3_id', 'id');
    }
}


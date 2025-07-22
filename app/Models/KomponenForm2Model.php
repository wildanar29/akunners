<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KomponenForm2Model extends Model
{
    protected $table = 'komponen_form_2';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function elemen()
    {
        return $this->belongsTo(ElemenKompetensiForm2::class, ['no_elemen', 'pk_id']);
    }
}

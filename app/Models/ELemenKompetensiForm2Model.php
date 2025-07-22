<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElemenKompetensiForm2Model extends Model
{
    protected $table = 'elemen_kompetensi_form_2';
    protected $primaryKey = 'no_elemen';
    public $timestamps = false;

    public function pk()
    {
        return $this->belongsTo(KompetensiPk::class, 'pk_id');
    }
}

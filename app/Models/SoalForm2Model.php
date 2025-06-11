<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoalForm2Model extends Model
{
    protected $table = 'soal_form_2';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function komponen()
    {
        return $this->belongsTo(KomponenForm2Model::class, 'komponen_id', 'komponen_id');
    }
}

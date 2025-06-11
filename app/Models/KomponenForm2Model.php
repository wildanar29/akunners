<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KomponenForm2Model extends Model
{
    protected $table = 'komponen_form_2';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function soals()
    {
        return $this->hasMany(SoalForm2Model::class, 'komponen_id', 'komponen_id');
    }
}

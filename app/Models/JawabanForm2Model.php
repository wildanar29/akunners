<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class JawabanForm2Model extends Model 
{
    protected $table = 'jawaban_form_2';
    protected $primaryKey = 'jawab_form_2_id';
    public $timestamps = false;

    protected $fillable = ['no_id', 'k', 'bk', 'user_jawab_form_2_id']; 

    public function soal()
    {
        return $this->hasMany(SoalForm2Model::class, 'no_id', 'no_id');
    }
    
}

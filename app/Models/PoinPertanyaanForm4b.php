<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoinPertanyaanForm4b extends Model
{
    protected $table = 'poin_pertanyaan_form4b';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'pertanyaan_form4b_id',
        'isi_poin',
        'urutan',
    ];

    // Relasi ke pertanyaan induknya
    public function pertanyaan()
    {
        return $this->belongsTo(PertanyaanForm4b::class, 'pertanyaan_form4b_id');
    }
}

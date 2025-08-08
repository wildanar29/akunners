<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PertanyaanForm4b extends Model
{
    protected $table = 'pertanyaan_form4b';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'iuk_form_3_id',
        'parent_id',
        'pertanyaan',
        'urutan',
    ];

    // Relasi ke IUK
    public function iukForm3()
    {
        return $this->belongsTo(IukForm3::class, 'iuk_form_3_id', 'iuk_form3_id');
    }

    // Relasi ke pertanyaan parent (jika bertingkat)
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    // Relasi ke children (untuk struktur pohon)
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    // Relasi ke poin indikator dari pertanyaan ini
    public function poinPertanyaan()
    {
        return $this->hasMany(PoinPertanyaanForm4b::class, 'pertanyaan_form4b_id');
    }
}

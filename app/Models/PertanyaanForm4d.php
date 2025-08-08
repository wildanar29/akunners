<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PertanyaanForm4d extends Model
{
    protected $table = 'pertanyaan_form4d';

    protected $fillable = [
        'kuk_form3_id',
        'parent_id',
        'dokumen',
        'urutan',
    ];

    /**
     * Relasi ke model KukModel.
     */
    public function kuk()
    {
        return $this->belongsTo(KukModel::class, 'kuk_form3_id', 'kuk_form3_id');
    }

    /**
     * Relasi ke parent-nya sendiri (self-reference).
     */
    public function parent()
    {
        return $this->belongsTo(PertanyaanForm4d::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(PertanyaanForm4d::class, 'parent_id');
    }
}

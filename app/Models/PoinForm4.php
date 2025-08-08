<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoinForm4 extends Model
{
    // Nama tabel
    protected $table = 'poin_form4';

    // Kolom yang boleh diisi (mass assignable)
    protected $fillable = [
        'iuk_form_3_id',
        'parent_id',
        'isi_poin',
        'urutan',
    ];

    // Jika tidak menggunakan timestamps otomatis
    public $timestamps = true;

    // Jika kolom created_at dan updated_at berbeda nama, sesuaikan:
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    // Relasi ke model lain (jika ada)
    public function parent()
    {
        return $this->belongsTo(PoinForm4::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(PoinForm4::class, 'parent_id');
    }

    public function iukForm3()
    {
        return $this->belongsTo(IukModel::class, 'iuk_form_3_id');
    }
}

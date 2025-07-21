<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KompetensiProgres extends Model
{
    protected $table = 'kompetensi_progres';

    protected $fillable = [
        'form_id',
        'parent_form_id',
        'user_id',
        'status',
    ];

    // Jika timestamp otomatis aktif
    public $timestamps = true;

    /**
     * Relasi ke user
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke form induk (form_1)
     */
    public function formInduk()
    {
        return $this->belongsTo(Form1::class, 'form_id');
    }

    /**
     * Relasi ke form parent (bisa form_1 atau form lainnya)
     */
    public function parentForm()
    {
        return $this->belongsTo(KompetensiProgres::class, 'parent_form_id');
    }

    /**
     * Relasi untuk mengambil turunan dari progres ini (anak-anak form)
     */
    public function children()
    {
        return $this->hasMany(KompetensiProgres::class, 'parent_form_id');
    }
}

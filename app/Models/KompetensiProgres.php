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
        return $this->belongsTo(BidangModel::class, 'form_id', 'form_1_id');
    }

    public function formChild()
    {
        return $this->belongsTo(PenilaianForm2Model::class, 'form_id', 'form_2_id');
        // Ganti dengan form lain jika bukan hanya form_2
    }

    /**
     * Relasi ke form parent (bisa form_1 atau form lainnya)
     */
    public function parentForm()
    {
        return $this->belongsTo(KompetensiProgres::class, 'parent_form_id');
    }

    public function form2()
    {
        return $this->belongsTo(PenilaianForm2Model::class, 'form_2_id'); // Pastikan kolom 'form_id' cocok
    }

    /**
     * Relasi untuk mengambil turunan dari progres ini (anak-anak form)
     */
    public function children()
    {
        return $this->hasMany(KompetensiProgres::class, 'parent_form_id');
    }

    public function tracks()
    {
        return $this->belongsTo(KompetensiTrack::class, 'track_id');
    }

    public function track()
    {
        return $this->hasOne(KompetensiTrack::class, 'progres_id');
    }

}

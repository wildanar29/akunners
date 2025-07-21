<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KompetensiTrack extends Model
{
    use HasFactory;

    protected $table = 'kompetensi_tracks';

    protected $fillable = [
        'progres_id',
        'form_type',
        'activity',
        'activity_time',
        'description',
    ];

    protected $casts = [
        'activity_time' => 'datetime',
    ];

    /**
     * Relasi ke model KompetensiProgres
     */
    public function progres()
    {
        return $this->belongsTo(KompetensiProgres::class, 'progres_id');
    }
}

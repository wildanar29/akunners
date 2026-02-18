<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pejabat extends Model
{
    use HasFactory;

    protected $table = 'pejabat'; // Nama tabel
    protected $primaryKey = 'id'; // Primary key
    public $timestamps = true; // Karena menggunakan created_at & updated_at

    protected $fillable = [
        'nama',
        'jabatan',
        'no_reg',
        'unit',
        'is_aktif',
        'user_id',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    // Relasi ke tabel users (nullable)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPE (Optional tapi sangat berguna)
    |--------------------------------------------------------------------------
    */

    // Scope untuk pejabat aktif
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    // Scope berdasarkan jabatan
    public function scopeJabatan($query, $jabatan)
    {
        return $query->where('jabatan', $jabatan);
    }
}

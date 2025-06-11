<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryJabatan extends Model
{
    use HasFactory;

    protected $table = 'history_jabatan_user'; // Nama tabel di database

    protected $primaryKey = 'user_jabatan_id'; // atau sesuai dengan primary key di tabel


    protected $fillable = [
        'user_id',
        'jabatan_id',
        'working_unit_id',
        'dari',
        'sampai',

    ]; // Isi dengan kolom yang ingin diisi secara massal

    // Jika ingin menggunakan timestamps (created_at & updated_at), biarkan default
    public $timestamps = true;

    /**
     * Relasi ke tabel users (DaftarUser)
     */
    public function user()
    {
        return $this->belongsTo(DaftarUser::class, 'user_id', 'user_id');
    }

    /**
     * Relasi ke tabel jabatan (JabatanModel)
     */
    public function jabatan()
    {
        return $this->belongsTo(JabatanModel::class, 'jabatan_id', 'jabatan_id');
    }

    /**
     * Relasi ke tabel working_unit (WorkingUnit)
     */
    public function workingUnit()
    {
        return $this->belongsTo(WorkingUnit::class, 'working_unit_id', 'working_unit_id');
    }
}

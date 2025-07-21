<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataAsesorModel extends Model
{
    use HasFactory;

    protected $table = 'data_asesor'; // Nama tabel di database
    protected $primaryKey = 'id_asesor'; // Primary key
    public $timestamps = false; // Jika tidak menggunakan created_at dan updated_at

    protected $fillable = [
        'nama_doc',
        'user_id',
        'no_reg',
        'tanggal_berlaku',
        'aktif',
        "valid_from",
        "valid_until",
    ];


    public function user()
    {
        return $this->belongsTo(DaftarUser::class, 'user_id', 'user_id');
    }



    
}

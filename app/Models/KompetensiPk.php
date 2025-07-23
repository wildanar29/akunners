<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KompetensiPk extends Model
{
    // Nama tabel di database
    protected $table = 'kompetensi_pk';

    // Primary key (jika bukan 'id')
    protected $primaryKey = 'pk_id';

    // Boleh diisi massal
    protected $fillable = [
        'nama_level',
        'deskripsi',
        'is_active',
    ];

    // Timestamps sudah ditangani otomatis
    public $timestamps = true;

    // Jika tidak pakai auto increment atau beda format, bisa atur di sini
    // public $incrementing = true;
    // protected $keyType = 'int';
    public function elemenForm3()
    {
        return $this->hasMany(ElemenForm3::class, 'pk_id', 'pk_id');
    }

}

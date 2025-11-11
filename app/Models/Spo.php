<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spo extends Model
{
    use HasFactory;

    protected $table = 'spo'; // Nama tabel di database
    protected $primaryKey = 'id'; // Primary key
    public $timestamps = true; // Karena ada kolom created_at dan updated_at

    protected $fillable = [
        'no_spo',
        'nama_spo',
        'pk_id',
        'row',
    ];

    /**
     * Relasi ke model KompetensiPk (jika tabel pk_id berelasi ke PK)
     */
    public function kompetensiPk()
    {
        return $this->belongsTo(KompetensiPk::class, 'pk_id', 'pk_id');
    }

    /**
     * Ambil data SPO berurutan berdasarkan nomor SPO
     */
    public static function getOrderedByNoSpo()
    {
        return self::orderBy('no_spo')->get();
    }
}

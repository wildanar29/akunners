<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanForm6 extends Model
{
    // Nama tabel
    protected $table = 'kegiatan_form6';

    // Primary key
    protected $primaryKey = 'id';

    // Gunakan timestamps
    public $timestamps = true;

    // Kolom yang dapat diisi
    protected $fillable = [
        'pk_id',
        'langkah_id',
        'deskripsi',
        'catatan',
        'pencapaian',
    ];

    // Casting kolom ke tipe data yang sesuai
    protected $casts = [
        'pencapaian' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function langkah()
    {
        return $this->belongsTo(LangkahForm6::class, 'langkah_id');
    }
    // App\Models\KegiatanForm6.php

    public function poin()
    {
        return $this->hasMany(PoinForm6::class, 'kegiatan_id')->whereNull('parent_id');
    }

    public function jawabanForm6()
    {
        return $this->hasMany(JawabanForm6::class, 'kegiatan_id');
    }
}

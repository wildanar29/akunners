<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PkStatusModel extends Model
{
    use HasFactory;

    protected $table = 'pk_status'; // Nama tabel di database
    protected $primaryKey = 'status_id'; // Primary key
    public $timestamps = false; // Jika tidak menggunakan created_at dan updated_at

    protected $fillable = [
        'progress_id',
        'form_1_status',
        'form_2_status',
        'form_3_status',
        'form_4_status',
        'form_5_status',
        'form_6_status',
        'form_7_status',
        'form_8_status',
        'form_9_status',
        'form_10_status',
        'form_11_status',
        'form_12_status',
    ];


    // Relasi ke PkProgressModel (many-to-one)
    public function progress()
    {
        return $this->belongsTo(PkProgressModel::class, 'progress_id', 'progress_id');
    }

    
}

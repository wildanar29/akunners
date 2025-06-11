<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PkProgressModel extends Model
{
    use HasFactory;

    protected $table = 'pk_progress'; // Nama tabel di database
    protected $primaryKey = 'progress_id'; // Primary key
    public $timestamps = false; // Jika tidak menggunakan created_at dan updated_at

    protected $fillable = [
        'user_id',
        'form_1_id',
        'form_2_id',
        'form_3_id',
        'form_4_id',
        'form_5_id',
        'form_6_id',
        'form_7_id',
        'form_8_id',
        'form_9_id',
        'form_10_id',
        'form_11_id',
        'form_12_id',
    ];


    // Relasi ke PkStatusModel (one-to-one)
    public function status()
    {
        return $this->hasOne(PkStatusModel::class, 'progress_id', 'progress_id');
    }

     // Relasi ke BidangModel (form_1_id -> form_1)
     public function form1()
     {
         return $this->belongsTo(BidangModel::class, 'form_1_id', 'form_1_id');
     }

    
}

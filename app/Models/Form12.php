<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Form12 extends Model
{
    use HasFactory;

    protected $table = 'form_12'; // nama tabel sesuai DB
    protected $primaryKey = 'form_12_id'; // primary key di tabel

    // kolom yang bisa diisi mass assignment
    protected $fillable = [
        'pk_id',
        'asesi_id',
        'asesi_name',
        'asesi_date',
        'asesor_id',
        'asesor_name',
        'asesor_date',
        'no_reg',
        'status',
    ];

    // jika tidak menggunakan incrementing integer untuk primary key bisa diubah
    public $incrementing = true;

    // kalau tipe primary key bukan string, tetap biarin integer
    protected $keyType = 'int';

    // otomatis pakai created_at & updated_at
    public $timestamps = true;

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */
    
    public function asesi()
    {
        return $this->belongsTo(User::class, 'asesi_id', 'user_id');
    }

    public function asesor()
    {
        return $this->belongsTo(User::class, 'asesor_id', 'user_id');
    }
}

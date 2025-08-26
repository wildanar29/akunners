<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form7 extends Model
{
    protected $table = 'form_7';
    protected $primaryKey = 'form_7_id';
    public $timestamps = true;

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

    /**
     * Relasi ke user asesi
     */
    public function asesi()
    {
        return $this->belongsTo(User::class, 'asesi_id', 'user_id');
    }

    /**
     * Relasi ke user asesor
     */
    public function asesor()
    {
        return $this->belongsTo(User::class, 'asesor_id', 'user_id');
    }
}

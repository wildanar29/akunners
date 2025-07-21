<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    protected $table = 'educations';

    // Jika tidak menggunakan timestamps (created_at, updated_at)
    public $timestamps = false;

    // Kolom yang bisa diisi massal
    protected $fillable = [
        'code',
        'name',
    ];
}

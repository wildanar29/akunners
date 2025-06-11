<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $table = 'user_role'; // Nama tabel
    protected $primaryKey = 'role_id'; // Pastikan ini adalah primary key yang benar
    public $incrementing = true; // Jika role_id adalah auto-increment
    protected $keyType = 'int'; // Jika role_id berupa integer

    protected $fillable = [
        'role_name', // Pastikan kolom ini ada
    ];
}

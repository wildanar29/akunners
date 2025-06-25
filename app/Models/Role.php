<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles'; // Nama tabel
    protected $primaryKey = 'role_id'; // Pastikan ini adalah primary key yang benar
    public $incrementing = true; // Jika role_id adalah auto-increment
    protected $keyType = 'int'; // Jika role_id berupa integer

    protected $fillable = [
        'role_name', // Pastikan kolom ini ada
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role', 'role_id', 'user_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $table = 'user_role'; // Tabel pivot

    public $timestamps = false; // Tabel pivot biasanya tidak punya timestamps

    protected $fillable = [
        'user_id',
        'role_id',
    ];

    // Relasi ke model User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke model Role
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}

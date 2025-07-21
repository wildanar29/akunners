<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    use HasFactory;

    // Menentukan nama tabel yang digunakan
    protected $table = 'password_reset';

    // Menentukan field yang bisa diisi (fillable)
    protected $fillable = [
        'no_telp', 
        'otp', 
        'expires_at', 
        'validate_otp_password',
        'email'
    ];

    // Menentukan apakah ID otomatis
    protected $primaryKey = 'id';
    
    // Menambahkan untuk tipe data boolean
    protected $casts = [
        'validate_otp_password' => 'boolean',
    ];

    // Jika waktu kadaluwarsa (`expires_at`) berbentuk timestamp, kita bisa menggunakan tipe cast untuk itu
    protected $dates = [
        'expires_at',
    ];
}

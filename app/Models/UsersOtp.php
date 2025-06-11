<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersOtp extends Model
{
    use HasFactory;

    // Tabel yang akan digunakan
    protected $table = 'users_otps';

    // Field yang dapat diisi (mass assignable)
    protected $fillable = [
        'email',       // Nomor telepon pengguna
        'otp',           // Kode OTP
        'created_at',    // Waktu pembuatan
        'expires_at',    // Waktu kedaluwarsa OTP
        'validate_otp',  // Status validasi (boolean)
    ];

    // Field yang didefinisikan sebagai tipe data tanggal
    protected $dates = ['created_at', 'expires_at'];

    public $timestamps = true; // Pastikan aktif

    /**
     * Relasi ke tabel users
     */

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IjazahModel extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users_ijazah_file';

    protected $primaryKey = 'ijazah_id'; // Ganti 'file_id' dengan kolom primary key tabel Anda


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'path_file',
        'user_id',
        'valid',
        'authentic',
        'current',
        'sufficient',
        'ket',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'kesesuaian_bukti' => 'boolean',
    ];

     public function user()
    {
        return $this->belongsTo(DaftarUser::class, 'user_id', 'user_id');
    }
}

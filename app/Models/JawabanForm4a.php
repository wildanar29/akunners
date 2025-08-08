<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JawabanForm4a extends Model
{
    protected $table = 'jawaban_form4a';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'form_1_id',
        'user_id',
        'iuk_form3_id',
        'pencapaian',
        'nilai',
        'catatan',
    ];

    // Relasi ke IukForm3
    public function iukForm3()
    {
        return $this->belongsTo(IukForm3::class, 'iuk_form3_id', 'iuk_form3_id');
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Relasi ke Form1
    public function form1()
    {
        return $this->belongsTo(Form1::class, 'form_1_id', 'form_1_id');
    }
}

<?php  

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form4cAttemptSummary extends Model
{
    protected $table = 'form4c_attempt_summary';

    protected $fillable = [
        'form_1_id',
        'user_id',
        'attempt',
        'tanggal_attempt',
        'total_jawaban',
        'jawaban_benar',
        'jawaban_salah',
        'nilai',
        'skor'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function form1()
    {
        return $this->belongsTo(Form1::class, 'form_1_id', 'form_1_id');
    }
}

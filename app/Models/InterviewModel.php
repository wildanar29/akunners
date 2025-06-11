<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterviewModel extends Model
{
    use HasFactory;

    protected $table = 'schedule_interview'; // Nama tabel di database
    protected $primaryKey = 'interview_id'; // Primary key
    public $timestamps = false; // Jika tidak menggunakan created_at dan updated_at

    protected $fillable = [
        'asesi_name',
        'asesor_name',
		'user_id',
        'date',
        'time',
        'place',
		'form_1_id',
		'asesor_id',
		'status',
    ];

    
}

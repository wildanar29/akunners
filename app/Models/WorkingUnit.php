<?php

// Dalam model WorkingUnit.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkingUnit extends Model
{
    protected $table = 'working_unit'; // nama tabel yang sesuai dengan database

    protected $primaryKey = 'working_unit_id'; // Harus sesuai dengan database  

    
}

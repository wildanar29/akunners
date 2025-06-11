<?php

// Dalam model WorkingUnit.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JabatanModel extends Model
{
    protected $table = 'jabatan'; // nama tabel yang sesuai dengan database

    protected $primaryKey = 'jabatan_id'; // Harus sesuai dengan database  

    
}

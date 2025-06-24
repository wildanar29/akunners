<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LangkahForm5 extends Model
{
    protected $table = 'langkah_form5';
    protected $fillable = ['nomor_langkah', 'judul_langkah', 'form_parent', 'catatan'];

    public function kegiatans()
    {
        return $this->hasMany(KegiatanForm5::class, 'langkah_id');
    }
}


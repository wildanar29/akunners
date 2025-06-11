<?php  
  
namespace App\Models;  
  
use Illuminate\Database\Eloquent\Factories\HasFactory;  
use Illuminate\Database\Eloquent\Model;  
  
class BidangModel extends Model  
{  
    use HasFactory;  
  
    // Nama tabel yang digunakan oleh model ini  
    protected $table = 'form_1';  
  
    // Primary key tabel  
    protected $primaryKey = 'form_1_id';  
  
    // Menandakan bahwa primary key adalah auto-increment  
    public $incrementing = true;  
  
    // Tipe data primary key  
    protected $keyType = 'int';  
  
    // Kolom yang dapat diisi (fillable)  
    protected $fillable = [
        'user_id',  
        'asesi_name',  
        'asesi_date',  
        'signature_asesi',  
        'asesor_name',  
        'asesor_date',  
        'signature_asesor',  
        'no_reg',  
        'status',  
        'ijazah_id',  
        'transkrip_id',  
        'sip_id',  
        'str_id',  
        'ujikom_id',
        'sertifikat_id'
    ];  
  
    // Kolom yang tidak dapat diisi (guarded)  
    protected $guarded = [];  
  
    // Kolom yang harus dikonversi ke tipe data tertentu  
    protected $casts = [  
        'asesi_date' => 'date',  
        'asesor_date' => 'date',  
    ];  

    // Menambahkan relasi ke model Sertifikat
    public function sertifikat()
    {
        return $this->belongsTo(SertifikatModel::class, 'sertifikat_id', 'sertifikat_id');
    }

    public function ijazah()
    {
        return $this->belongsTo(IjazahModel::class, 'ijazah_id', 'ijazah_id');
    }

    public function transkrip()
    {
        return $this->belongsTo(TranskripModel::class, 'transkrip_id', 'transkrip_id');
    }

    public function ujikom()
    {
        return $this->belongsTo(TranskripModel::class, 'ujikom_id', 'ujikom_id');
    }

    public function str()
    {
        return $this->belongsTo(StrModel::class, 'str_id', 'str_id');
    }

    public function sip()
    {
        return $this->belongsTo(SipModel::class, 'sip_id', 'sip_id');
    }


    // Relasi ke tabel pk_progress (One-to-One)
    public function form1()
    {
        return $this->hasOne(PkProgressModel::class, 'form_1_id', 'form_1_id');
    }


}  

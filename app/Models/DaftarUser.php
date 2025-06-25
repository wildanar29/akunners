<?php  
  
namespace App\Models;  
  
use Illuminate\Database\Eloquent\Model;  
use App\Models\Role;  
use App\Models\WorkingUnit;  
use Tymon\JWTAuth\Contracts\JWTSubject; // Tambahkan ini    
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;    
use Laravel\Sanctum\HasApiTokens; // Jika menggunakan Sanctum
use Illuminate\Auth\Authenticatable; // Pastikan ini diimpor


class DaftarUser extends Model implements AuthenticatableContract, JWTSubject
{  
    use Authenticatable, HasApiTokens;

    protected $table = 'users';  
  
    protected $primaryKey = 'user_id'; // Primary key adalah user_id  
    public $incrementing = true;      // Jika user_id adalah auto-increment  
    protected $keyType = 'int';       // Jika user_id berupa integer  
  
    protected $fillable = [  
        'nik',  
        'nama',  
        'password',  
        'tempat_lahir',  
        'tanggal_lahir',  
        'kewarganegaraan',  
        'jenis_kelamin',  
        'pendidikan',  
        'tahun_lulus',  
        'provinsi',  
        'kota',  
        'alamat',  
        'kode_pos',  
        'email',  
        'jabatan_id',
        'masa_berlaku_jabatan',
        'no_telp',  
        'role_id',  
        'working_unit_id',  
        'foto',
        'device_token'  
    ];  
      
    protected $hidden = [  
        'password',  
    ];  
      
    // Relasi dengan Role  
    public function role()  
    {  
        return $this->belongsTo(Role::class, 'role_id');  
    }  

    public function dataAsesor()
    {
        return $this->hasOne(DataAsesorModel::class, 'user_id', 'user_id');
    }

  
    // Relasi dengan WorkingUnit  
    public function workingUnit()  
    {  
        return $this->belongsTo(WorkingUnit::class, 'working_unit_id');  
    }  
  
    public function jabatan()  
    {  
        return $this->belongsTo(JabatanModel::class, 'jabatan_id', 'jabatan_id');  
    }  
  
    // Implementasi metode dari JWTSubject    
    public function getJWTIdentifier()    
    {    
        return $this->getKey(); // Mengembalikan ID pengguna    
    }    
    
    public function getJWTCustomClaims()    
    {    
        return []; // Anda dapat menambahkan klaim kustom jika diperlukan    
    }    
  
    // Metode yang diperlukan untuk autentikasi    
    public function getAuthIdentifierName()    
    {    
        return 'user_id'; // Mengembalikan nama kolom yang digunakan untuk autentikasi    
    }    
  
    public function getAuthIdentifier()    
    {    
        return $this->getKey(); // Mengembalikan nilai dari kolom user_id    
    }    
  
    public function getRememberToken()    
    {    
        return null; // Jika tidak menggunakan remember token, kembalikan null    
    }    
  
    public function setRememberToken($value)    
    {    
        // Tidak perlu diimplementasikan jika tidak menggunakan remember token    
    }    
  
    // Implementasi metode tambahan  
    public function getAuthPassword()  
    {  
        return $this->password; // Mengembalikan password pengguna  
    }  
  
    public function getRememberTokenName()  
    {  
        return 'remember_token'; // Ganti dengan nama kolom yang sesuai jika Anda menggunakan remember token  
    }  

    public function ijazah()
    {
        return $this->hasOne(IjazahModel::class, 'user_id', 'user_id');
    }

    public function Sip()
    {
        return $this->hasOne(SipModel::class, 'user_id', 'user_id');
    }

    public function Spk()
    {
        return $this->hasOne(SpkModel::class, 'user_id', 'user_id');
    }

    public function Str()
    {
        return $this->hasOne(StrModel::class, 'user_id', 'user_id');
    }
    
    public function Transkrip()
    {
        return $this->hasOne(TranskripModel::class, 'user_id', 'user_id');
    }

    public function Ujikom()
    {
        return $this->hasOne(UjikomModel::class, 'user_id', 'user_id');
    }

    public function Sertifikat()
    {
        return $this->hasMany(SertifikatModel::class, 'user_id', 'user_id');
    }




}  

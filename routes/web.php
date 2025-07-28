<?php


/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// MASTER DATA
$router->get('/get-educations', 'MasterController@getEducations');
$router->get('/get-kompetensi-pk', 'MasterController@getKompetensiPk');
$router->get('/get-elemen-pk/{pk_id}', 'MasterController@getElemenAsesmen');

// PROGRES TRACKING
$router->get('/progress/asesi/{asesorId}', 'AsesiPermohonanController@getAsesiProgressByAsesor');
$router->get('/progress/assessment/{asesi_id}', 'ProgressController@getProgresByAsesi');
// FORM 1 ATAU FORM PENGAJUAN
$router->get('/progress/{userId}', 'AsesiPermohonanController@getUserFormProgress');
$router->post('/get-form1-byasesor', 'AsesiPermohonanController@getForm1ByAsesor');
$router->post('/get-form1-byasesi', 'AsesiPermohonanController@getForm1ByAsesi');

// Bagian API Form 5
$router->post('/konsultasi/pra-asesmen', 'Form5Controller@pengajuanKonsultasiPraAsesmen');
$router->get('/jadwal/interview', 'Form5Controller@getJadwalInterviewGabungan');
$router->post('/interview/update-status', 'Form5Controller@updateStatusInterview');
$router->post('/interview/bidang', 'Form5Controller@getJadwalInterviewByBidang');
$router->get('/form5/langkah-kegiatan', 'Form5Controller@getLangkahDanKegiatan');
$router->post('/form5/jawaban-kegiatan', 'Form5Controller@simpanJawabanKegiatan');
$router->get('/form5/soal-jawab', 'Form5Controller@getLangkahKegiatanDenganJawaban');
$router->get('/form5/asesi-approve', 'Form5Controller@approveKompetensiProgres');


$router->get('/tracks-by-form', 'ProgressController@getTracksByFormId');
// Bagian API Form 3

//Ambil Kisi-Kisi Form3
$router->get('/get-form3-a', action: 'Form3Controller@getAllDataFormA');
$router->get('/get-form3-b', action: 'Form3Controller@getAllDataFormB');
$router->get('/get-form3-c', action: 'Form3Controller@getAllDataFormC');
$router->get('/get-form3-d', action: 'Form3Controller@getAllDataFormD');
// baru
$router->post('/approve-form3-asesi', action: 'Form3Controller@ApproveAsesiForm3');
$router->put('/form3/update/{form3_id}', 'Form3Controller@UpdateAsesorForm3');
// akhir baru
//API Form 3 Sampai Sini



// AUTH CONTROLLER 
$router->post('/register-akun', 'UsersController@RegisterAkunNurse');
$router->post('/login-akun', 'UsersController@LoginAkunNurse');
$router->post('/update-profile/{nik}', 'UsersController@UpdateAkunNurse');
$router->get('/get-profile/{nik}', 'UsersController@GetAkunNurseByNIK');
$router->get('/check-profile/{nik}', 'UsersController@CheckDataCompleteness');
$router->post('/create-password', 'UsersController@createPassword');
$router->post('/input-jabatan-working/{nik}', 'UsersController@insertHistoryJabatan');
$router->put('/edit-jabatan-working/{nik}', 'UsersController@updateHistoryJabatan');
// baru
$router->delete('/delete-jabatan-working/{nik}', 'UsersController@deleteHistoryJabatan');
// akhir baru

//Reset Password
$router->post('/new-password', 'UsersController@newPassword');


//WhastappController 
$router->post('/send-otp', 'WhatsappController@sendOtp');
$router->get('/valid-otp', 'WhatsappController@validateOtp');
$router->post('/reset-otp', 'WhatsappController@resetOtp');  


//WhastappController Reset Password
$router->post('/send-otp-reset-password', 'MailController@sendOtpPassword');
$router->get('/valid-otp-reset-password', 'MailController@validateOtpPassword');
$router->post('/reset-otp-password', 'WhatsappController@resetOtpPassword');


//Working atau List Ruangan 
$router->get('/get-list-working-units', 'GetWorkingUnitController@index'); // Ambil semua data

//Ambil data Jabatan
$router->get('/get-list-jabatan', 'JabatanController@getAllJabatan'); // Ambil semua data

//Ambil data Jabatan
$router->get('/get-indikator-status/{user_id}', 'FormStatusController@getFormStatusByUser'); // Ambil semua data


//Ijazah Controller
$router->group(['middleware' => 'auth'], function () use ($router) {  
$router->post('/upload-ijazah', 'IjazahController@upload');  
$router->put('/update-ijazah/{nik}', 'IjazahController@updateFile');
$router->delete('/ijazah/file/{nik}', 'IjazahController@deleteFile');
$router->get('/storage/ijazah/{path}', function ($path) {  
    return response()->file(storage_path('app/public/ijazah/' . $path));  
});  


//Sip Controller
$router->post('/upload-sipp', 'SipController@upload');
$router->put('/update-sipp/{nik}', 'SipController@updateFile');
$router->delete('/sipp/file/{nik}', 'SipController@deleteFile');
$router->get('/get-no-expired-sipp/{nik}', 'SipController@getSipByNik');
$router->get('/storage/sip/{path}', function ($path) {  
    return response()->file(storage_path('app/public/Sip/' . $path));  
});  


//Str Controller
$router->post('/upload-str', 'StrController@upload');
$router->put('/update-str/{nik}', 'StrController@updateFile');
$router->delete('/str/file/{nik}', 'StrController@deleteFile');
$router->get('/get-no-expired-str/{nik}', 'StrController@getStrByNik');
$router->get('/storage/str/{path}', function ($path) {  
    return response()->file(storage_path('app/public/Str/' . $path));  
});  


//Spk Controller
$router->post('/upload-spk', 'SpkController@upload');
$router->put('/update-spk/{nik}', 'SpkController@updateFile');
$router->delete('/spk/file/{nik}', 'SpkController@deleteFile');
$router->get('/get-no-expired-spk/{nik}', 'SpkController@getSpkByNik');
$router->get('/storage/spk/{path}', function ($path) {  
    return response()->file(storage_path('app/public/Spk/' . $path));  
});  

//SAK Controller
$router->post('/upload-sak', 'SakController@upload');
$router->put('/update-sak/{nik}', 'SakController@updateFile');
$router->delete('/sak/file/{nik}', 'SakController@deleteFile');
$router->get('/get-no-expired-sak/{nik}', 'SakController@getSakByNik');
$router->get('/storage/sak/{path}', function ($path) {  
    return response()->file(storage_path('app/public/Sak/' . $path));  
});  

//Ujikom Controller
$router->post('/upload-ujikom', 'UjikomController@upload');
$router->put('/update-ujikom/{nik}', 'UjikomController@updateFile');
$router->delete('/ujikom/file/{nik}', 'UjikomController@deleteFile');
$router->get('/get-no-expired-ujikom/{nik}', 'UjikomController@getUjikomByNik');
$router->get('/storage/ujikom/{path}', function ($path) {  
    return response()->file(storage_path('app/public/Ujikom/' . $path));  
});   


//Sertifikat Controller
$router->post('/upload-sertifikat', 'SertifikatController@upload');
$router->put('/update-sertifikat/{nik}', 'SertifikatController@updateFile');
$router->delete('/sertifikat/file/{nik}/{sertifikat_id}', 'SertifikatController@deleteFile');
$router->get('/get-no-expired-sertifikat/{nik}', 'SertifikatController@getSertifikatByNik');
$router->get('/storage/sertifikat/{path}', function ($path) {  
    return response()->file(storage_path('app/public/Sertifikat/' . $path));  
});   


//Bidang Controller
$router->get('/get-list-asesor', 'BidangController@getListDataAsesor');
$router->post('/ajuan-asesi', 'AsesiPermohonanController@AjuanPermohonanAsesi');
$router->put('/input-asesor', 'BidangController@insertAsesor');
$router->put('/input-status/{form_1_id}', 'BidangController@updateStatus');
$router->get('/get-form1', 'BidangController@getAllForm1');
$router->get('/get-form1-by-id/{form_1_id}', 'BidangController@getForm1ById');
$router->get('/get-form1-by-id-date', 'BidangController@getForm1ByDate');

// Asesor Controller
$router->get('/form1/asesor/{asesorName}', 'AsesorController@getForm1ByAsesorName');
$router->put('/form1/approve/{form_1_id}', 'AsesorController@approveForm1ById');
// baru
$router->post('/jawaban-form2/update/asesor', 'AsesorController@updateJawabanForm2ByNoId');
// akhir baru
$router->put('/jawaban-form2/update-if-empty/{user_jawab_form2_id}', 'AsesorController@updateIfEmptyByUserJawabForm2Id');
$router->post('/notification', 'NotificationController@getNotifications');
$router->post('/notification/read', 'NotificationController@markAsRead');
// Cukup Sampai Sini Form 1

 
//Bagian API Form2


//Soal Form 2
$router->get('/soal-form2', 'Form2Controller@getSoals');
$router->get('/soal-form2/{no_elemen}', 'Form2Controller@getSoalsByNoElemen');
$router->get('/soal-form2-id/{no_id}', 'Form2Controller@getSoalsByNoId');
//Jawaban dan Penilaian Form 2
$router->post('/jawaban-asesi', 'Form2Controller@JawabanAsesi');
$router->get('/get-jawaban-form2/{user_jawab_form_2_id}', 'Form2Controller@getDataSoalJawaban');
$router->put('/penilaian-asesor', 'Form2Controller@inputPenilaianAsesor');
//Get Form 2
$router->get('/get-form2', 'Form2Controller@getForm2Data');
$router->get('/get-form2-id/{form_2_id}', 'Form2Controller@getForm2ById');
$router->get('/get-form2-by-date-id', 'Form2Controller@getForm2ByIdAndDate');
$router->get('/get-form2-by-no-reg', 'Form2Controller@getForm2ByNoReg');
// Cukup Sampai Sini Form 2
$router->get('/get-soal-jawab-form2', 'Form2Controller@getSoalDanJawaban');

//Asesor Form3 Input Approved
$router->post('/input-form3/{user_id}', 'Form3Controller@Form3Input');

$router->get('/form1', 'ProgressController@getForm1');


//Notifikasi 
$router->get('/send-notification-to-bidang', action: 'NotificationController@notifikasiPengajuankeBidang');
});// buat Authentikasi


$router->post('/user/update-role', 'BidangController@updateUserRole');


$router->get('/swagger.json', function () {
    return response()->file(public_path('swagger.json'));
});

$router->get('/api-docs.json', function () {
    return response()->file(storage_path('api-docs/api-docs.json'));
});


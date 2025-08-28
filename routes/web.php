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
$router->post('/upload-sip', 'SipController@upload');
$router->put('/update-sip/{nik}', 'SipController@updateFile');
$router->delete('/sip/file/{nik}', 'SipController@deleteFile');
$router->get('/get-no-expired-sip/{nik}', 'SipController@getSipByNik');
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

// FORM 4A
$router->get('/form4a/elemen-filter', 'Form4aController@getSoalForm4a');
$router->post('/form4a/jawaban', 'Form4aController@simpanJawabanForm4a');
$router->get('/form4a/soal-jawaban', 'Form4aController@getSoalDanJawabanForm4a');
$router->post('/form4a/{form4aId}/approve-asesi', 'Form4aController@ApproveForm4aByAsesi');

// FORM 4B
$router->get('/form4b/soal', 'Form4bController@getSoalForm4b');
$router->post('/form4b/jawaban', 'Form4bController@storeJawabanForm4b');
$router->get('/form4b/soal-jawaban', 'Form4bController@getSoalDanJawabanForm4b');
$router->post('/form4b/{form4bId}/approve-asesi', 'Form4bController@ApproveForm4bByAsesi');

// FORM 4C
$router->get('/form4c/soal', 'Form4cController@getAllPertanyaanForm4c');
$router->post('/form4c/jawaban', 'Form4cController@storeJawabanForm4c');
$router->get('/form4c/soal-jawaban', 'Form4cController@getSoalDanJawabanForm4c');
$router->post('/form4c/{form4cId}/approve-asesi', 'Form4cController@ApproveForm4cByAsesi');

// FORM 4D
$router->get('/form4d/soal', 'Form4dController@getSoalForm4dByPkId');
$router->post('/form4d/jawaban', 'Form4dController@simpanJawabanForm4d');
$router->get('/form4d/soal-jawaban', 'Form4dController@getSoalDanJawabanForm4d');
$router->post('/form4d/{form4dId}/approve-asesi', 'Form4dController@ApproveForm4dByAsesi');

// FORM 6
$router->get('/form6/soal/{pkId}', 'Form6Controller@SoalForm6');
$router->post('/form6/jawaban', 'Form6Controller@simpanJawabanForm6');
$router->get('/form6/soal-jawab/{pkId}', 'Form6Controller@getSoalDanJawabanForm6');
$router->post('/form6/approve', 'Form6Controller@ApproveForm6ByAsesi');


// FORM 7
$router->get('/form7/soal/{pkId}', 'Form7Controller@getSoalForm7');
$router->post('/form7/jawaban', 'Form7Controller@simpanBanyakJawabanForm7');
$router->get('/form7/soal-jawaban/{pkId}/{asesiId}', 'Form7Controller@getSoalDanJawabanForm7');
$router->get('/form7/iuk/{pkId}', 'Form7Controller@getIukForm3IdFromForm7');
$router->get('/form7/keputusan/{pkId}/{form1Id}', 'Form7Controller@getAllKeputusanForm7');
$router->post('/form7/{form7Id}/approve-asesi', 'Form7Controller@ApproveForm7ByAsesi');

// FORM 8
$router->post('/form8/banding/store', 'Form8Controller@storeFormBandingAsesmen');
$router->post('/form8/banding/{bandingId}/approve', 'Form8Controller@approveFormBandingAsesmen');

// FORM 9
$router->group(['prefix' => 'form9'], function () use ($router) {
    // ambil pertanyaan berdasarkan subject (section)
    $router->get('/questions', 'Form9Controller@getQuestionsBySubject');
    $router->get('/{form9Id}/soal-jawab', 'Form9Controller@getQuestionsAndAnswersByFormId');
     $router->post('/{form9Id}/save-jawaban', 'Form9Controller@saveOrUpdateAnswers');
});

// FORM 10 DAFTAR TILIK
$router->get('/form10/daftar-tilik', 'Form10Controller@getAll');
$router->get('/form10/soal/{form10Id}', 'Form10Controller@getSoalList');
$router->post('/form10/{form10Id}/submit', 'Form10Controller@submitSoalList');
$router->get('/form10/soal-jawab/{form10Id}', 'Form10Controller@getForm10WithAnswersById');
$router->post('/form10/{form10Id}/approve-asesi', 'Form10Controller@ApproveForm10ByAsesi');

// FORM 12
$router->post('/form12/by-pk', 'Form12Controller@getByPkId');
$router->post('/form12/{form12Id}/approve-asesi', 'Form12Controller@ApproveForm12ByAsesi');

$router->get('/form6/soal/{pkId}', 'Form6Controller@SoalForm6');


// CERTIFICATE
$router->get('/tes-view', function () {
    return view('sertifikat', ['nama' => 'Wildan', 'tanggal' => date('d F Y')]);
});

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


$router->get('/test-form-service', function () {
    $service = app(\App\Service\FormService::class);
    
    $asesiId = 68; // ganti sesuai dengan data di database kamu
    $pkId = 1; // ganti sesuai dengan data di database kamu
    $form_type = 'form_6'; // ganti sesuai dengan jenis form yang ingin dicek
    $exists = $service->isFormExist($asesiId, $pkId, $form_type);

    return response()->json([
        'isForm6Exists' => $exists,
    ]);
});

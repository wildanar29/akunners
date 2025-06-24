<?php  
  
namespace App\Http\Controllers;  
  
use Illuminate\Http\Request;  
use App\Models\BidangModel;  
use App\Models\UserRole;
use App\Models\DaftarUser; // Pastikan untuk mengimpor model User  
use App\Models\IjazahModel; // Model untuk users_ijazah_file  
use App\Models\TranskripModel; // Model untuk users_transkrip_file  
use App\Models\SipModel; // Model untuk users_sip_file  
use App\Models\StrModel; // Model untuk users_str_file  
use App\Models\UjikomModel; // Model untuk users_str_file  
use App\Models\DataAsesorModel; // Model untuk users_str_file  
use App\Models\PkProgressModel; // Model untuk users_str_file  
use App\Models\PkStatusModel; // Model untuk users_str_file  
use Illuminate\Support\Facades\Validator;  
use App\Models\HistoryJabatan;
use App\Models\User; // Model untuk user  
use Illuminate\Support\Facades\DB; // Tambahkan DB untuk query manual
use Carbon\Carbon; // Pastikan untuk mengimpor Carbon  


  
class BidangController extends Controller  
{  

 /**  
 * @OA\Put(  
 *     path="/input-asesor",  
 *     summary="Update nama Asesor yang akan menilai Asesi berdarkan nik, form_1_id dan Input no_reg",  
 *     tags={"Bidang"},  
 *     @OA\RequestBody(  
 *         required=true,  
 *         @OA\JsonContent(  
 *             required={"nik", "form_1_id", "no_reg"},  
 *             @OA\Property(property="nik", type="integer", example=102, description="NIK of the user to be updated as Asesor"),  
 *             @OA\Property(property="form_1_id", type="integer", example=12, description="ID of the form_1 record to be updated"),  
 *             @OA\Property(property="no_reg", type="string", example="REG123456", description="Registration number of the Asesor")  
 *         )  
 *     ),  
 *     @OA\Response(  
 *         response=200,  
 *         description="Data asesor berhasil diperbarui.",  
 *         @OA\JsonContent(  
 *             @OA\Property(property="success", type="boolean", example=true),  
 *             @OA\Property(property="message", type="string", example="Data asesor berhasil diperbarui."),  
 *             @OA\Property(property="status_code", type="integer", example=200)  
 *         )  
 *     ),  
 *     @OA\Response(  
 *         response=400,  
 *         description="Validasi gagal.",  
 *         @OA\JsonContent(  
 *             @OA\Property(property="success", type="boolean", example=false),  
 *             @OA\Property(property="message", type="string", example="Validasi gagal. Pastikan NIK dan form_1_id valid."),  
 *             @OA\Property(property="errors", type="object"),  
 *             @OA\Property(property="status_code", type="integer", example=400)  
 *         )  
 *     ),  
 *     @OA\Response(  
 *         response=404,  
 *         description="User tidak ditemukan atau bukan Asesor.",  
 *         @OA\JsonContent(  
 *             @OA\Property(property="success", type="boolean", example=false),  
 *             @OA\Property(property="message", type="string", example="User tidak ditemukan atau bukan Asesor."),  
 *             @OA\Property(property="status_code", type="integer", example=404)  
 *         )  
 *     ),  
 *     @OA\Response(  
 *         response=500,  
 *         description="Terjadi kesalahan saat memperbarui data asesor.",  
 *         @OA\JsonContent(  
 *             @OA\Property(property="success", type="boolean", example=false),  
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat memperbarui data asesor."),  
 *             @OA\Property(property="error", type="string"),  
 *             @OA\Property(property="status_code", type="integer", example=500)  
 *         )  
 *     )  
 * )  
 */   
    public function insertAsesor(Request $request)  
	{  
		// Validasi hak akses: hanya role_id = 3 yang diizinkan
		if (auth()->user()->role_id != 3) {
			return response()->json([
				'success' => false,
				'message' => 'Anda tidak memiliki izin untuk melakukan aksi ini.',
				'status_code' => 403,
			], 403);
		}

		// Validasi input
		$validation = Validator::make($request->all(), [  
			'no_reg' => 'required|string|max:255',
			'form_1_id' => 'required|exists:form_1,form_1_id',
		]);  

		if ($validation->fails()) {  
			return response()->json([  
				'success' => false,  
				'message' => 'Validation failed. Ensure no_reg and form_1_id are valid.',
				'errors' => $validation->errors(),  
				'status_code' => 400,  
			], 400);  
		}  

		try {  
			// Cari user berdasarkan no_reg di tabel data_asesor
			$asesor = DataAsesorModel::where('no_reg', $request->no_reg)
						->where('aktif', 1)
						->first();

			if (!$asesor) {  
				return response()->json([  
					'success' => false,  
					'message' => 'No active asesor found with the given no_reg.',
					'status_code' => 404,  
				], 404);  
			}  

			$user = DaftarUser::where('user_id', $asesor->user_id)->first();

			if (!$user) {  
				return response()->json([  
					'success' => false,  
					'message' => 'User data not found for the given no_reg.',
					'status_code' => 404,  
				], 404);  
			}  

			$asesor_name = $user->nama;  
			$asesor_date = Carbon::now();  

			$bidang = BidangModel::find($request->form_1_id);  
			if (!$bidang) {  
				return response()->json([  
					'success' => false,  
					'message' => 'Data not found for the given form_1_id.',
					'status_code' => 404,  
				], 404);  
			}  

			$bidang->asesor_id = $user->user_id;
			$bidang->asesor_name = $asesor_name;  
			$bidang->asesor_date = $asesor_date;  
			$bidang->no_reg = $request->no_reg;  
			$bidang->save();  

			return response()->json([  
				'success' => true,  
				'message' => 'Asesor data successfully updated.',
				'data' => $bidang,  
				'status_code' => 200,  
			], 200);  

		} catch (\Exception $e) {  
			return response()->json([  
				'success' => false,  
				'message' => 'An error occurred while updating asesor data.',
				'error' => $e->getMessage(),  
				'status_code' => 500,  
			], 500);  
		}  
	}


 /**  
 * @OA\Put(  
 *     path="/input-status/{form_1_id}",  
 *     summary="Update status in form_1",  
 *     tags={"Asesor"},  
 *     @OA\Parameter(  
 *         name="form_1_id",  
 *         in="path",  
 *         required=true,  
 *         description="ID of the form_1 to update",  
 *         @OA\Schema(type="integer")  
 *     ),  
 *     @OA\Response(  
 *         response=200,  
 *         description="Status berhasil diperbarui.",  
 *         @OA\JsonContent(  
 *             @OA\Property(property="success", type="boolean", example=true),  
 *             @OA\Property(property="message", type="string", example="Status berhasil diperbarui."),  
 *             @OA\Property(property="status_code", type="integer", example=200)  
 *         )  
 *     ),  
 *     @OA\Response(  
 *         response=400,  
 *         description="Validasi gagal atau tidak semua penilaian bernilai true.",  
 *         @OA\JsonContent(  
 *             @OA\Property(property="success", type="boolean", example=false),  
 *             @OA\Property(property="message", type="string", example="Tidak semua penilaian bernilai true."),  
 *             @OA\Property(property="status_code", type="integer", example=400)  
 *         )  
 *     ),  
 *     @OA\Response(  
 *         response=404,  
 *         description="Data tidak ditemukan untuk form_1_id yang diberikan.",  
 *         @OA\JsonContent(  
 *             @OA\Property(property="success", type="boolean", example=false),  
 *             @OA\Property(property="message", type="string", example="Data tidak ditemukan untuk form_1_id yang diberikan."),  
 *             @OA\Property(property="status_code", type="integer", example=404)  
 *         )  
 *     ),  
 *     @OA\Response(  
 *         response=500,  
 *         description="Terjadi kesalahan saat memperbarui status.",  
 *         @OA\JsonContent(  
 *             @OA\Property(property="success", type="boolean", example=false),  
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat memperbarui status."),  
 *             @OA\Property(property="error", type="string"),  
 *             @OA\Property(property="status_code", type="integer", example=500)  
 *         )  
 *     )  
 * )  
 */  
    public function updateStatus(Request $request, $form_1_id)  
    {  
        try {  
            // Cari entri berdasarkan form_1_id  
            $bidang = BidangModel::find($form_1_id);  
            if (!$bidang) {  
                return response()->json([  
                    'success' => false,  
                    'message' => 'Data not found for the given form_1_id.',
                    'status_code' => 404,  
                ], 404);  
            }  

            // Ambil user_id dari tabel form_1 (jika memang user_id yang digunakan)  
            $user_id = $bidang->user_id;  


            // Cari progress_id di pk_progress berdasarkan user_id  
            $progress = PkProgressModel::where('user_id', $user_id)->first();  
            if (!$progress) {
                return response()->json([
                    'success' => false,
                    'message' => 'No progress record found for this user_id.',
                    'status_code' => 404,
                ], 404);
            }

            // Cek status penilaian di tabel terkait berdasarkan user_id  
            $ijazah = IjazahModel::where('user_id', $user_id)->first();  
            $str = StrModel::where('user_id', $user_id)->first();  
            $sip = SipModel::where('user_id', $user_id)->first();  
            $ujikom = UjikomModel::where('user_id', $user_id)->first();  

            // Memeriksa apakah semua status adalah true  
            $allTrue = $ijazah && $ijazah->valid && $ijazah->authentic && $ijazah->current && $ijazah->sufficient &&  
                    $str && $str->valid && $str->authentic && $str->current && $str->sufficient &&  
                    $sip && $sip->valid && $sip->authentic && $sip->current && $sip->sufficient &&  
                    $ujikom && $ujikom->valid && $ujikom->authentic && $ujikom->current && $ujikom->sufficient;

            // Jika semua true, status menjadi 'Approved', jika tidak 'Cancel'
            $bidang->status = $allTrue ? 'Approved' : 'Cancel';
            $bidang->save();  
            

            // Jika status Approved, update form_1_status & form_2_status di tabel pk_status
            if ($bidang->status === 'Approved') {
                PkStatusModel::where('progress_id', $progress->progress_id)->update([
                    'form_1_status' => 'Completed',
                    'form_2_status' => 'Open'
                ]);
            } elseif ($bidang->status === 'Cancel') {
                // Jika status Cancel, tetap form_1_status = Open dan form_2_status = NULL
                PkStatusModel::where('progress_id', $progress->progress_id)->update([
                    'form_1_status' => 'Open',
                    'form_2_status' => null
                ]);
            }

            // Ambil data terbaru dari pk_status setelah update
            $updatedStatus = PkStatusModel::where('progress_id', $progress->progress_id)->first();
            
            return response()->json([  
                'success' => true,  
                'message' => 'Status successfully updated.',
                'data' => $bidang,
                'form_1_status' => $updatedStatus->form_1_status ?? null,
                'form_2_status' => $updatedStatus->form_2_status ?? null,
                'status_code' => 200,  
            ], 200);  

        } catch (\Exception $e) {  
            return response()->json([  
                'success' => false,  
                'message' => 'An error occurred while updating the status.',
                'error' => $e->getMessage(),  
                'status_code' => 500,  
            ], 500);  
        }  
    }  


  /**
 * @OA\Post(
 *     path="/get-form1",
 *     summary="Ambil semua data Form1 (opsional filter status)",
 *     tags={"Bidang"},
 *     @OA\RequestBody(
 *         required=false,
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status",
 *                 type="string",
 *                 example="Waiting",
 *                 description="Opsional filter berdasarkan status: Waiting, ApprovedBy_Asesor, ApprovedBy_Bidang, Cancel, Completed"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="OK"),
 *             @OA\Property(property="message", type="string", example="Data berhasil diambil."),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="form_1_id", type="integer", example=53),
 *                     @OA\Property(property="user_id", type="integer", example=39),
 *                     @OA\Property(property="asesi_name", type="string", example="Testing99"),
 *                     @OA\Property(property="asesi_date", type="string", format="date-time"),
 *                     @OA\Property(property="asesor_name", type="string", example="Dalia Novitasari"),
 *                     @OA\Property(property="asesor_date", type="string", format="date-time"),
 *                     @OA\Property(property="no_reg", type="string", example="ASK.123456"),
 *                     @OA\Property(property="status", type="string", example="Cancel"),
 *                     @OA\Property(property="ijazah_id", type="integer", example=38),
 *                     @OA\Property(property="spk_id", type="integer", nullable=true, example=null),
 *                     @OA\Property(property="sip_id", type="integer", example=17),
 *                     @OA\Property(property="str_id", type="integer", example=14),
 *                     @OA\Property(property="ujikom_id", type="integer", example=14),
 *                     @OA\Property(property="sertifikat_id", type="integer", example=23),
 *                     @OA\Property(property="created_at", type="string", format="date-time"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time"),
 *                     @OA\Property(property="foto", type="string", format="url", example="http://yourdomain.com/storage/foto_nurse/foto.jpg")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Status tidak valid",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERR"),
 *             @OA\Property(property="message", type="string", example="Status tidak valid."),
 *             @OA\Property(property="data", type="string", example=null)
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan server",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERR"),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan: ..."),
 *             @OA\Property(property="data", type="string", example=null)
 *         )
 *     )
 * )
 */


   public function getAllForm1(Request $request)
	{
		try {
			$status = $request->input('status');

			$allowedStatus = [
				'Waiting',
				'ApprovedBy_Asesor',
				'ApprovedBy_Bidang',
				'Cancel',
				'Completed'
			];

			if ($status && !in_array($status, $allowedStatus)) {
				return response()->json([
					'status' => 'ERR',
					'message' => 'Status tidak valid.',
					'data' => null
				], 400);
			}

			$form1Data = $status
				? BidangModel::where('status', $status)->get()
				: BidangModel::all();

			// Tambahkan foto dari DaftarUser berdasarkan user_id
			$form1Data = $form1Data->map(function ($item) {
				$user = DaftarUser::find($item->user_id);

				$item->foto = $user && $user->foto
					? url('storage/foto_nurse/' . basename($user->foto))
					: null;

				return $item;
			});

			return response()->json([
				'status' => 'OK',
				'message' => 'Data berhasil diambil.',
				'data' => $form1Data,
			], 200);

		} catch (\Exception $e) {
			return response()->json([
				'status' => 'ERR',
				'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
				'data' => null
			], 500);
		}
	}


    /**
 * @OA\Get(
 *     path="/get-form1-by-id/{form_1_id}",
 *     summary="Mengambil form_1 data dari form_1_id",
 *     tags={"Bidang"},
 *     @OA\Parameter(
 *         name="form_1_id",
 *         in="path",
 *         required=true,
 *         description="form_1_id yang akan diambil",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data form_1 berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Data form_1 berhasil diambil"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 properties={
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="Bidang A"),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-11T00:00:00.000000Z"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-11T00:00:00.000000Z")
 *                 }
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data form_1 tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Data form_1 tidak ditemukan")
 *         )
 *     )
 * )
 */

	 public function getForm1ById($form_1_id)
	 {
		 try {
			 $form1Data = BidangModel::find($form_1_id);
	 
			 $userData = null;
			 if ($form1Data && $form1Data->user_id) {
				 $userData = DaftarUser::where('user_id', $form1Data->user_id)->first();
			 }
	 
			 $form1Array = $form1Data ? $form1Data->toArray() : [];
			 $userArray = [];
	 
			 if ($userData) {
				 // Ambil data role
				 $role = UserRole::where('role_id', $userData->role_id)->first();
				 $role_name = $role ? $role->role_name : null;
	 
				 // Ambil semua history jabatan
				 $historyJabatan = HistoryJabatan::where('user_id', $userData->user_id)->get();
				 $jabatanData = $historyJabatan->map(function ($history) {
					 $working_unit = DB::table('working_unit')->where('working_unit_id', $history->working_unit_id)->first();
					 $jabatan = DB::table('jabatan')->where('jabatan_id', $history->jabatan_id)->first();
	 
					 return [
						 'working_unit_id' => $history->working_unit_id,
						 'working_unit_name' => $working_unit ? $working_unit->working_unit_name : null,
						 'jabatan_id' => $history->jabatan_id,
						 'nama_jabatan' => $jabatan ? $jabatan->nama_jabatan : null,
						 'dari' => $history->dari,
						 'sampai' => $history->sampai
					 ];
				 });
	 
				 // Ambil dokumen berdasarkan ID di form1Data
				 $ijazahFile = DB::table('users_ijazah_file')->where('ijazah_id', $form1Data->ijazah_id)->first();
				 $ujikomFile = DB::table('users_ujikom_file')->where('ujikom_id', $form1Data->ujikom_id)->first();
				 $strFile = DB::table('users_str_file')->where('str_id', $form1Data->str_id)->first();
				 $sipFile = DB::table('users_sip_file')->where('sip_id', $form1Data->sip_id)->first();
				 $spkFile = DB::table('users_spk_file')->where('spk_id', $form1Data->spk_id)->first(); // jika diperlukan
	 
				 $ijazah = [
					 'url' => $ijazahFile ? url('storage/' . $ijazahFile->path_file) : null
				 ];
	 
				 $ujikom = [
					 'url' => $ujikomFile ? url('storage/' . $ujikomFile->path_file) : null,
					 'nomor' => $ujikomFile->nomor_kompetensi ?? null,
					 'masa_berlaku' => $ujikomFile->masa_berlaku_kompetensi ?? null
				 ];
	 
				 $str = [
					 'url' => $strFile ? url('storage/' . $strFile->path_file) : null,
					 'nomor' => $strFile->nomor_str ?? null,
					 'masa_berlaku' => $strFile->masa_berlaku_str ?? null
				 ];
	 
				 $sip = [
					 'url' => $sipFile ? url('storage/' . $sipFile->path_file) : null,
					 'nomor' => $sipFile->nomor_sip ?? null,
					 'masa_berlaku' => $sipFile->masa_berlaku_sip ?? null
				 ];
	 
				 $spk = [
					 'url' => $spkFile ? url('storage/' . $spkFile->path_file) : null,
					 'nomor' => $spkFile->nomor_spk ?? null,
					 'masa_berlaku' => $spkFile->masa_berlaku_spk ?? null
				 ];
	 
				 // Pastikan userData memiliki sertifikat yang valid
				 $sertifikat = [];
				 if ($form1Data->user_id) {
					 // Ambil data sertifikat berdasarkan user_id dari form1Data
					 $sertifikatData = DB::table('users_sertifikat_pendukung')
										 ->where('user_id', $form1Data->user_id)
										 ->first();
	 
					 if ($sertifikatData) {
						 $sertifikat = [
							 'url' => url('storage/' . $sertifikatData->path_file),
							 'nomor' => $sertifikatData->nomor_sertifikat ?? null,
							 'masa_berlaku' => $sertifikatData->masa_berlaku_sertifikat ?? null
						 ];
					 }
				 }
	 
				 // Susun user array
				 $userArray = [
					 'nama' => $userData->nama,
					 'email' => $userData->email,
					 'no_telp' => $userData->no_telp,
					 'tempat_lahir' => $userData->tempat_lahir,
					 'tanggal_lahir' => $userData->tanggal_lahir,
					 'kewarganegaraan' => $userData->kewarganegaraan,
					 'jenis_kelamin' => $userData->jenis_kelamin,
					 'pendidikan' => $userData->pendidikan,
					 'tahun_lulus' => $userData->tahun_lulus,
					 'provinsi' => $userData->provinsi,
					 'kota' => $userData->kota,
					 'alamat' => $userData->alamat,
					 'kode_pos' => $userData->kode_pos,
					 'role_id' => $userData->role_id,
					 'role_name' => $role_name,
					 'jabatan_history' => $jabatanData,
					 'foto' => $userData->foto ? url('storage/foto_nurse/' . basename($userData->foto)) : null,
					 'ijazah' => $ijazah,
					 'ujikom' => $ujikom,
					 'str' => $str,
					 'sip' => $sip,
					 'spk' => $spk,
					 'sertifikat' => $sertifikat,
				 ];
			 }
	 
			 // Gabungkan data form1Data dan userData
			 $mergedData = array_merge($form1Array, $userArray);
	 
			 return response()->json([
				 'status' => 200,
				 'message' => 'Data berhasil ditemukan.',
				 'data' => $mergedData
			 ], 200);
		 } catch (\Exception $e) {
			 return response()->json([
				 'status' => 500,
				 'message' => 'Terjadi kesalahan pada server.',
				 'error' => $e->getMessage()
			 ], 500);
		 }
	 }


    /**
 * @OA\Get(
 *     path="/form1/by-date",
 *     summary="Get form_1 data by form_1_id and date range",
 *     tags={"Bidang"},
 *     @OA\Parameter(
 *         name="form_1_id",
 *         in="query",
 *         required=true,
 *         description="ID of the form_1 to retrieve",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="start_date",
 *         in="query",
 *         required=true,
 *         description="Start date of the range",
 *         @OA\Schema(type="string", format="date")
 *     ),
 *     @OA\Parameter(
 *         name="end_date",
 *         in="query",
 *         required=true,
 *         description="End date of the range",
 *         @OA\Schema(type="string", format="date")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data form_1 berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Data form_1 berhasil diambil"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     properties={
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="name", type="string", example="Bidang A"),
 *                         @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-11T00:00:00.000000Z"),
 *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-11T00:00:00.000000Z")
 *                     }
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validasi gagal, parameter tidak valid",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Validasi gagal"),
 *             @OA\Property(property="errors", type="object", additionalProperties={
 *                 @OA\Property(property="form_1_id", type="array", items={
 *                     @OA\Property(type="string", example="The form_1_id must be an integer.")
 *                 }),
 *                 @OA\Property(property="date", type="array", items={
 *                     @OA\Property(type="string", example="The date is required.")
 *                 })
 *             })
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data form_1 tidak ditemukan dalam rentang tanggal tersebut",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Data form_1 tidak ditemukan dalam rentang tanggal tersebut")
 *         )
 *     )
 * )
 */


    public function getForm1ByDate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'form_1_id' => 'required|integer|exists:form_1,form_1_id',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation error', 'errors' => $validator->errors()], 400);
        }

        $form1Data = BidangModel::where('form_1_id', $request->form_1_id)
            ->whereBetween('created_at', [$request->start_date, $request->end_date])
            ->get();

        if ($form1Data->isEmpty()) {
            return response()->json(['message' => 'Form_1 data was not found in that date range'], 404);
        }

        return response()->json([
            'message' => 'Data form_1 berhasil diambil',
            'data' => $form1Data
        ], 200);
    }


    /**
 * @OA\Get(
 *     path="/get-list-asesor",
 *     summary="Get list of active data asesor",
 *     description="Mendapatkan daftar asesor yang aktif berdasarkan tabel data_asesor, serta menampilkan nomor registrasi dan nama dari tabel users.",
 *     tags={"Data Asesor"},
 *     @OA\Response(
 *         response=200,
 *         description="List of active data asesor",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="string", example="SUCCESS"),
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="no_reg", type="string", example="12345"),
 *                     @OA\Property(property="nama", type="string", example="Budi Santoso")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="No active asesor found",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="string", example="SUCCESS"),
 *             @OA\Property(property="message", type="string", example="No active asesor found."),
 *             @OA\Property(property="data", type="array", example={})
 *         )
 *     )
 * )
 */



    public function getListDataAsesor()
    {
        // Ambil data dari tabel data_asesor, gabungkan dengan tabel users berdasarkan user_id
        $dataAsesor = DataAsesorModel::join('users', 'data_asesor.user_id', '=', 'users.user_id')
            ->where('data_asesor.aktif', 1) // Hanya ambil yang aktif
            ->select('data_asesor.no_reg', 'users.nama') // Pilih kolom yang ingin ditampilkan
            ->get();

            // Jika tidak ada data, kirim response kosong
            if ($dataAsesor->isEmpty()) {
                return response()->json([
                    'status' => 'SUCCESS',
                    'message' => 'No active asesor found.',
                    'data' => []
                ]);
            }


        // Return response JSON
        return response()->json([
            'status' => 'SUCCESS',
            'data' => $dataAsesor
        ]);
    }

	public function updateUserRole(Request $request)
	{
		// Validasi input dari request
		$validator = Validator::make($request->all(), [
			'user_id' => 'required|exists:users,user_id',
			'role_id' => 'required|integer',
		]);

		if ($validator->fails()) {
			return response()->json([
				'success' => false,
				'message' => 'Validasi gagal.',
				'errors' => $validator->errors(),
				'status_code' => 400,
			], 400);
		}

		try {
			// Cari user
			$user = DaftarUser::find($request->user_id);

			// Jika ingin mengubah role menjadi Asesor (role_id = 2), validasi data_asesor
			if ($request->role_id == 2) {
				$asesorTerdaftar = DataAsesorModel::where('user_id', $user->user_id)
									->where('aktif', 1)
									->exists();

				if (!$asesorTerdaftar) {
					return response()->json([
						'success' => false,
						'message' => 'User belum terdaftar sebagai asesor aktif.',
						'status_code' => 403,
					], 403);
				}
			}

			// Update role_id user
			$user->role_id = $request->role_id;
			$user->save();

			return response()->json([
				'success' => true,
				'message' => 'Role user berhasil diubah.',
				'data' => [
					'user_id' => $user->user_id,
					'nama' => $user->nama,
					'role_id_baru' => $user->role_id,
				],
				'status_code' => 200,
			], 200);

		} catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => 'Terjadi kesalahan saat mengubah role.',
				'error' => $e->getMessage(),
				'status_code' => 500,
			], 500);
		}
	}





}  
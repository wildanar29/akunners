<?php  
  
namespace App\Http\Controllers;  
  
use Illuminate\Http\Request;  
use App\Models\BidangModel;  
use App\Models\Role;
use App\Models\KompetensiProgres;
use App\Models\KompetensiTrack;
use App\Models\Notification;
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
use Illuminate\Support\Facades\Log;

use App\Service\OneSignalService;
  
class BidangController extends Controller  
{  
	protected $oneSignalService;

	public function __construct(OneSignalService $oneSignalService)
	{
		$this->oneSignalService = $oneSignalService;
	}

   public function insertAsesor(Request $request)  
	{
		$user = auth()->user();
		Log::info('Memulai insertAsesor', ['user_id' => $user->user_id ?? null]);

		// Cek hak akses role_id = 3
		$hasAccess = $user && $user->roles->contains('role_id', 3);

		if (!$hasAccess) {
			Log::warning('Akses ditolak: user tidak memiliki role_id 3', ['user_id' => $user->user_id ?? null]);

			return response()->json([
				'success' => false,
				'message' => 'Anda tidak memiliki izin untuk melakukan aksi ini.',
				'status_code' => 403,
			], 403);
		}

		// Validasi input
		$validation = Validator::make($request->all(), [
			'status' => 'nullable|in:Rejected',
			'no_reg' => 'nullable|string|max:255',
			'form_1_id' => 'required|exists:form_1,form_1_id',
			'keterangan' => 'required_if:status,Rejected|string|nullable',
		]);

		if ($validation->fails()) {
			Log::warning('Validasi gagal saat insertAsesor', [
				'errors' => $validation->errors()->toArray(),
				'request' => $request->all(),
			]);

			return response()->json([
				'success' => false,
				'message' => 'Validasi gagal. Pastikan input sesuai.',
				'errors' => $validation->errors(),
				'status_code' => 400,
			], 400);
		}

		try {
			Log::info('Validasi berhasil, mencari data bidang', ['form_1_id' => $request->form_1_id]);

			$bidang = BidangModel::find($request->form_1_id);
			if (!$bidang) {
				Log::warning('Bidang tidak ditemukan', ['form_1_id' => $request->form_1_id]);

				return response()->json([
					'success' => false,
					'message' => 'Data tidak ditemukan untuk form_1_id.',
					'status_code' => 404,
				], 404);
			}

			if ($request->status === 'Rejected') {
				Log::info('Status Rejected diterima, memperbarui data bidang dan progres', ['form_1_id' => $bidang->form_1_id]);

				$bidang->status = 'Rejected';
				$bidang->ket = $request->keterangan;
				$bidang->save();

				// ✅ Update status di kompetensi_progres
				$progres = KompetensiProgres::where('form_id', $bidang->form_1_id)->first();
				if ($progres) {
					$progres->status = 'Rejected';
					$progres->save();

					Log::info('Status kompetensi_progres diperbarui menjadi Rejected', [
						'form_id' => $bidang->form_1_id,
						'progres_id' => $progres->id,
					]);

					// Tambahkan aktivitas ke kompetensi_tracks
					KompetensiTrack::create([
						'progres_id' => $progres->id,
						'form_type' => 'form_1',
						'activity' => 'Rejected',
						'activity_time' => Carbon::now(),
						'description' => 'Form 1 ditolak oleh bidang. Keterangan: ' . $request->keterangan,
					]);
				} else {
					Log::warning('Data kompetensi_progres tidak ditemukan untuk form_id', ['form_id' => $bidang->form_1_id]);
				}

				return response()->json([
					'success' => true,
					'message' => 'Status berhasil diubah menjadi Rejected.',
					'data' => $bidang,
					'status_code' => 200,
				], 200);
			}


			Log::info('Status Process diterima, memproses data asesor', ['no_reg' => $request->no_reg]);

			$asesor = DataAsesorModel::where('no_reg', $request->no_reg)
						->where('aktif', 1)
						->first();

			if (!$asesor) {
				Log::warning('Asesor tidak ditemukan atau tidak aktif', ['no_reg' => $request->no_reg]);

				return response()->json([
					'success' => false,
					'message' => 'Tidak ditemukan asesor aktif dengan no_reg tersebut.',
					'status_code' => 404,
				], 404);
			}

			Log::info('Asesor ditemukan, mencari user asesor', ['user_id' => $asesor->user_id]);

			$userAsesor = DaftarUser::where('user_id', $asesor->user_id)->first();
			if (!$userAsesor) {
				Log::warning('User asesor tidak ditemukan', ['user_id' => $asesor->user_id]);

				return response()->json([
					'success' => false,
					'message' => 'Data user tidak ditemukan untuk no_reg tersebut.',
					'status_code' => 404,
				], 404);
			}

			Log::info('User asesor ditemukan, menyimpan data ke bidang', [
				'user_id' => $userAsesor->user_id,
				'nama' => $userAsesor->nama,
			]);

			// Pastikan asesor bukan asesi yang bersangkutan
			if ($userAsesor->user_id == $bidang->asesi_id) {
				Log::warning('Asesor sama dengan Asesi, penugasan ditolak', [
					'form_1_id' => $bidang->form_1_id,
					'user_id' => $userAsesor->user_id,
				]);

				return response()->json([
					'status' => 'Error',
					'message' => 'Asesor tidak boleh sama dengan Asesi.',
					'status_code' => 400,
				], 400);
			}

			$bidang->asesor_id = $userAsesor->user_id;
			$bidang->asesor_name = $userAsesor->nama;
			$bidang->asesor_date = Carbon::now();
			$bidang->no_reg = $request->no_reg;
			$bidang->status = 'Assigned';
			$bidang->ket = null;
			$bidang->save();

			// ✅ Update status di kompetensi_progres
			$progres = KompetensiProgres::where('form_id', $bidang->form_1_id)->first();
			if ($progres) {
				$progres->status = 'Assigned';
				$progres->save();

				Log::info('Status kompetensi_progres diperbarui menjadi in_progress', [
					'form_id' => $bidang->form_1_id,
					'progres_id' => $progres->id,
				]);

				// Tambahkan aktivitas ke kompetensi_tracks
				KompetensiTrack::create([
					'progres_id' => $progres->id,
					'form_type' => 'form_1',
					'activity' => 'Assigned',
					'activity_time' => Carbon::now(),
					'description' => 'Form 1 disetujui dan asesor ditetapkan.',
				]);
			} else {
				Log::warning('Data kompetensi_progres tidak ditemukan untuk form_id', ['form_id' => $bidang->form_1_id]);
			}

			Log::info('Data bidang berhasil diupdate dengan asesor', ['form_1_id' => $bidang->form_1_id]);
			$this->kirimNotifikasiKeAsesor($userAsesor, $bidang->form_1_id);

			return response()->json([
				'success' => true,
				'message' => 'Data asesor berhasil diupdate.',
				'data' => $bidang,
				'status_code' => 200,
			], 200);

		} catch (\Exception $e) {
			Log::error('Terjadi exception saat insertAsesor', [
				'user_id' => $user->user_id ?? null,
				'form_1_id' => $request->form_1_id ?? null,
				'no_reg' => $request->no_reg ?? null,
				'error_message' => $e->getMessage(),
				'trace' => $e->getTraceAsString(),
			]);

			return response()->json([
				'success' => false,
				'message' => 'Terjadi kesalahan saat memproses data.',
				'error' => $e->getMessage(),
				'status_code' => 500,
			], 500);
		}
	}

	private function kirimNotifikasiKeAsesor(DaftarUser $userAsesor, $formId)
	{
		if (empty($userAsesor->device_token)) {
			Log::warning("Asesor user_id={$userAsesor->user_id} tidak memiliki device_token.");
			return;
		}

		try {
			$title = 'Penugasan Asesor';
			$message = "Anda memiliki tugas asesmen baru. cek disini.";

			// Kirim notifikasi ke OneSignal
			$this->oneSignalService->sendNotification(
				[$userAsesor->device_token],
				$title,
				$message
			);

			// Simpan notifikasi ke database
			Notification::create([
				'user_id' => $userAsesor->user_id,
				'title' => $title,
				'description' => $message,
				'is_read' => 0,
				'created_at' => Carbon::now(),
				'updated_at' => Carbon::now(),
			]);

			Log::info("Notifikasi penugasan dikirim ke asesor user_id={$userAsesor->user_id}, nama={$userAsesor->nama}");

		} catch (\Exception $e) {
			Log::error("Gagal mengirim notifikasi ke asesor.", [
				'user_id' => $userAsesor->user_id,
				'error_message' => $e->getMessage(),
				'error_trace' => $e->getTraceAsString(),
			]);
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
		$status = $request->query('status'); // Ambil dari query string

		$allowedStatus = [
			'Submitted',
			'Assigned',
			'Process',
			'Approved',
			'Process',
			'Completed',
			'Rejected',
			'InAssessment',
			'Verified',
			'Certified',
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
				// pastikan eager load roles jika belum
				$userData = DaftarUser::with('roles')->where('user_id', $form1Data->user_id)->first();
			}

			$form1Array = $form1Data ? $form1Data->toArray() : [];
			$userArray = [];

			if ($userData) {
				// Ambil semua history jabatan
				$historyJabatan = HistoryJabatan::where('user_id', $userData->user_id)->get();
				$jabatanData = $historyJabatan->map(function ($history) {
					$working_unit = DB::table('working_unit')->where('working_unit_id', $history->working_unit_id)->first();
					$jabatan = DB::table('jabatan')->where('jabatan_id', $history->jabatan_id)->first();

					return [
						'working_unit_id' => $history->working_unit_id,
						'working_unit_name' => $working_unit->working_unit_name ?? null,
						'jabatan_id' => $history->jabatan_id,
						'nama_jabatan' => $jabatan->nama_jabatan ?? null,
						'dari' => $history->dari,
						'sampai' => $history->sampai
					];
				});

				// Ambil dokumen berdasarkan ID di form1Data
				$ijazahFile = DB::table('users_ijazah_file')->where('ijazah_id', $form1Data->ijazah_id)->first();
				$ujikomFile = DB::table('users_ujikom_file')->where('ujikom_id', $form1Data->ujikom_id)->first();
				$strFile = DB::table('users_str_file')->where('str_id', $form1Data->str_id)->first();
				$sipFile = DB::table('users_sip_file')->where('sip_id', $form1Data->sip_id)->first();
				$spkFile = DB::table('users_spk_file')->where('spk_id', $form1Data->spk_id)->first();

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

				// Ambil sertifikat pendukung
				$sertifikat = [];
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
					'roles' => $userData->roles->map(function ($role) {
						return [
							'role_id' => $role->role_id,
							'role_name' => $role->role_name
						];
					}),
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
		$validator = Validator::make($request->all(), [
			'user_id' => 'required|exists:users,user_id',
			'role_id' => 'required|exists:roles,role_id',
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
			$user = DaftarUser::findOrFail($request->user_id);

			// Jika role yang diberikan adalah Asesor (role_id = 2), lakukan validasi tambahan
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

			// Tambahkan role ke user melalui tabel pivot user_role
			// Jika ingin mengganti role utama (override):
			$user->roles()->sync([$request->role_id]);

			// Jika hanya ingin menambahkan role (tanpa menghapus role yang lain):
			// $user->roles()->syncWithoutDetaching([$request->role_id]);

			return response()->json([
				'success' => true,
				'message' => 'Role user berhasil diperbarui.',
				'data' => [
					'user_id' => $user->user_id,
					'nama' => $user->nama,
					'role_ids' => $user->roles()->pluck('role_id'), // semua role yang dimiliki user
				],
				'status_code' => 200,
			], 200);

		} catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => 'Terjadi kesalahan saat memperbarui role.',
				'error' => $e->getMessage(),
				'status_code' => 500,
			], 500);
		}
	}





}  
<?php

namespace App\Http\Controllers;

use App\Models\IjazahModel;
use Illuminate\Support\Facades\Log;
use App\Service\OneSignalService;
use App\Models\DaftarUser;
use App\Models\KompetensiProgres;
use App\Models\KompetensiTrack;
use App\Models\KompetensiPk;
use App\Models\UserRole;
use App\Models\SipModel;
use App\Models\Notification;
use App\Models\StrModel;
use App\Models\SertifikatModel;
use App\Models\UjikomModel;
use App\Models\BidangModel; // Model form_1
use Illuminate\Support\Facades\DB;
use App\Models\PkProgressModel;
use Illuminate\Support\Facades\Validator; 
use App\Models\PkStatusModel; 
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\UsersController; // Ganti dengan nama controller yang berisi CheckDataCompleteness

class AsesiPermohonanController extends Controller
{

	protected $oneSignalService;

    public function __construct(OneSignalService $oneSignalService)
    {
        $this->oneSignalService = $oneSignalService;
    }

	public function AjuanPermohonanAsesi(Request $request)
	{
		try {
			$user = auth()->user();

			if (!$user) {
				return response()->json([
					'success' => false,
					'message' => 'Unauthorized. Invalid token or user not found.',
					'status_code' => 401,
				], 401);
			}

			$validator = Validator::make($request->all(), [
				'pk_id' => 'required|exists:kompetensi_pk,pk_id',
			]);

			if ($validator->fails()) {
				return response()->json([
					'success' => false,
					'message' => 'The pk id field is required.',
					'errors' => $validator->errors(),
					'status_code' => 422,
				], 422);
			}

			$pkId = (int) $request->input('pk_id');

			if ($pkId > 1) {
				$previousPk = BidangModel::where('asesi_id', $user->user_id)
					->where('pk_id', $pkId - 1)
					->where('status', 'Completed')
					->first();

				if (!$previousPk) {
					return response()->json([
						'success' => false,
						'message' => "You must complete PK " . ($pkId - 1) . " before submitting PK {$pkId}.",
						'status_code' => 403,
					], 403);
				}
			}

			$alreadySubmitted = BidangModel::where('asesi_id', $user->asesi_id)
				->where('pk_id', $pkId)
				->first();

			if ($alreadySubmitted) {
				return response()->json([
					'status' => 'SUCCESS',
					'message' => 'You have already submitted a request for the selected PK.',
					'status_code' => 409,
				], 409);
			}

			// ✅ Cek kelengkapan data profil
			$dataChecker = new UsersController();
			$checkDataResponse = $dataChecker->CheckDataCompleteness($user->nik);
			if ($checkDataResponse->getStatusCode() !== 200) {
				return $checkDataResponse;
			}

			$ijazah = IjazahModel::where('user_id', $user->user_id)->first();
			$str = StrModel::where('user_id', $user->user_id)->first();
			$sip = SipModel::where('user_id', $user->user_id)->first();
			$sertifikat = SertifikatModel::where('user_id', $user->user_id)->first();

			$missingDocuments = [];
			if (!$ijazah || empty($ijazah->path_file)) $missingDocuments[] = 'Ijazah';
			if (!$str || empty($str->path_file)) $missingDocuments[] = 'STR';
			if (!$sip || empty($sip->path_file)) $missingDocuments[] = 'SIP';

			if (!empty($missingDocuments)) {
				return response()->json([
					'success' => false,
					'message' => 'Submission failed. Missing: ' . implode(', ', $missingDocuments),
					'missing_documents' => $missingDocuments,
					'status_code' => 400,
				], 400);
			}

			// ✅ Transaksi DB
			DB::beginTransaction();

			$dataUpdate = [
				'pk_id' => $pkId,
				'asesi_name' => $user->nama,
				'asesi_date' => Carbon::now()->toDateString(),
				'ijazah_id' => $ijazah->ijazah_id,
				'str_id' => $str->str_id,
				'sip_id' => $sip->sip_id,
				'sertifikat_id' => $sertifikat ? $sertifikat->user_id : null,
				'status' => 'Submitted',
				'updated_at' => Carbon::now(),
			];

			if ($existingBidang = BidangModel::where('asesi_id', $user->user_id)->first()) {
				$existingBidang->update($dataUpdate);

				$this->kirimNotifikasiKeBidang($user->nama);

				DB::commit();

				return response()->json([
					'success' => true,
					'message' => 'Data successfully updated in Form_1.',
					'form_1_id' => $existingBidang->form_1_id,
					'updated_data' => $dataUpdate,
					'status_code' => 200,
				], 200);
			} else {
				$dataUpdate['asesi_id'] = $user->user_id;
				$dataUpdate['created_at'] = Carbon::now();

				$newBidang = BidangModel::create($dataUpdate);
				$form_1_id = $newBidang->form_1_id;

				$progres = KompetensiProgres::create([
					'form_id' => $form_1_id,
					'parent_form_id' => null,
					'user_id' => $user->user_id,
					'status' => 'Submitted',
				]);

				KompetensiTrack::create([
					'progres_id' => $progres->id,
					'form_type' => 'form_1',
					'activity' => 'Submitted',
					'activity_time' => Carbon::now(),
					'description' => 'Pengajuan Form 1 oleh asesi.',
				]);

				DB::commit();
				$this->kirimNotifikasiKeBidang($user->nama);
				return response()->json([
					'success' => true,
					'message' => 'Data successfully inserted into Form_1.',
					'form_1' => [
						'form_1_id' => $form_1_id,
						'user_id' => $user->user_id,
						'pk_id' => $pkId,
						'asesi_name' => $dataUpdate['asesi_name'],
						'asesi_date' => $dataUpdate['asesi_date'],
						'ijazah_id' => $dataUpdate['ijazah_id'],
						'str_id' => $dataUpdate['str_id'],
						'sip_id' => $dataUpdate['sip_id'],
						'sertifikat_id' => $dataUpdate['sertifikat_id'],
						'status' => $dataUpdate['status'],
						'created_at' => $dataUpdate['created_at'],
						'updated_at' => $dataUpdate['updated_at'],
					],
					'status_code' => 201,
				], 201);
			}
		} catch (\Exception $e) {
			DB::rollBack(); // rollback transaksi jika ada error

			\Log::error('Gagal melakukan pengajuan asesi.', [
				'user_id' => auth()->check() ? auth()->user()->user_id : null,
				'nama' => auth()->check() ? auth()->user()->nama : null,
				'error_message' => $e->getMessage(),
				'error_trace' => $e->getTraceAsString(),
				'line' => $e->getLine(),
				'file' => $e->getFile(),
			]);

			return response()->json([
				'success' => false,
				'message' => 'An unexpected error occurred while processing data.',
				'error' => $e->getMessage(),
				'status_code' => 500,
			], 500);
		}
	}

	private function kirimNotifikasiKeBidang($namaAsesi)
	{
		$bidangUsers = DaftarUser::whereHas('roles', function ($query) {
			$query->where('user_role.role_id', 3);
		})->get();
		Log::info("Mengirim notifikasi ke " . count($bidangUsers) . " user bidang.");
		Log::info($bidangUsers);
		$title = 'Pengajuan Asessmen';
		$message = "Ada pengajuan baru dari Asesi $namaAsesi.";

		foreach ($bidangUsers as $bidangUser) {
			// Hanya kirim dan simpan notifikasi jika user memiliki device_token
			if (!empty($bidangUser->device_token)) {
				// Kirim OneSignal
				$this->oneSignalService->sendNotification(
					[$bidangUser->device_token],
					$title,
					$message
				);

				// Simpan ke database
				Notification::create([
					'user_id' => $bidangUser->user_id,
					'title' => $title,
					'description' => $message,
					'is_read' => 0,
					'created_at' => Carbon::now(),
					'updated_at' => Carbon::now(),
				]);

				Log::info("Notifikasi dikirim ke user_id={$bidangUser->user_id}, nama={$bidangUser->nama}");
			} else {
				Log::warning("User bidang user_id={$bidangUser->user_id} tidak memiliki device_token.");
			}
		}
	}


   public function getUserFormProgress($userId)
	{
		try {
			// Ambil data pk_progress dan join ke masing-masing form yang sudah tersedia
			$progress = DB::table('pk_progress')
				->where('pk_progress.user_id', $userId)
				->leftJoin('form_1', 'pk_progress.form_1_id', '=', 'form_1.form_1_id')
				->leftJoin('form_2', 'pk_progress.form_2_id', '=', 'form_2.form_2_id')
				->leftJoin('form_3', 'pk_progress.form_3_id', '=', 'form_3.form_3_id')
				// ->leftJoin('form_4', ...)
				// dst...
				->select(
					'pk_progress.*',
					'form_1.status as form_1_status',
					'form_2.status as form_2_status',
					'form_3.status as form_3_status'
					// 'form_4.status as form_4_status',
					// dst...
				)
				->first();

			if (!$progress) {
				return response()->json([
					'status' => false,
					'message' => 'Data progress tidak ditemukan untuk user ini.',
					'data' => null
				], 404);
			}

			return response()->json([
				'status' => true,
				'message' => 'Berhasil mengambil data progres form.',
				'data' => [
					'user_id' => $userId,
					'form_statuses' => [
						'form_1' => $progress->form_1_status ?? 'Terkunci',
						'form_2' => $progress->form_2_status ?? 'Terkunci',
						'form_3' => $progress->form_3_status ?? 'Terkunci',
						// 'form_4' => $progress->form_4_status ?? 'Belum Diisi',
						// dst...
					]
				]
			]);
		} catch (\Exception $e) {
			// Tangani error tak terduga
			return response()->json([
				'status' => false,
				'message' => 'Terjadi kesalahan saat mengambil data progres.',
				'data' => null,
				'error' => $e->getMessage() // opsional, hapus jika tak ingin expose error detail
			], 500);
		}
	}

	public function getAsesiProgressByAsesor($asesorId)
	{
		try {
			// Ambil semua data progres asesi yang dibimbing oleh asesor ini
			$progressList = DB::table('pk_progress')
				->where('pk_progress.asesor_id', $asesorId)
				->leftJoin('form_1', 'pk_progress.form_1_id', '=', 'form_1.form_1_id')
				->leftJoin('form_2', 'pk_progress.form_2_id', '=', 'form_2.form_2_id')
				->leftJoin('form_3', 'pk_progress.form_3_id', '=', 'form_3.form_3_id')
				->leftJoin('users', 'pk_progress.user_id', '=', 'users.user_id') // asesi info
				->select(
					'pk_progress.user_id',
					'users.nama as user_name',
					'users.foto as user_foto', // pastikan kolom 'foto' ada di tabel 'users'
					'form_1.status as form_1_status',
					'form_2.status as form_2_status',
					'form_3.status as form_3_status'
				)
				->get();

			if ($progressList->isEmpty()) {
				return response()->json([
					'status' => false,
					'message' => 'Tidak ditemukan data progres untuk asesor ini.',
					'data' => []
				], 404);
			}

			// Siapkan array hasil
			$data = $progressList->map(function ($progress) {
				return [
					'user_id' => $progress->user_id,
					'user_name' => $progress->user_name,
					'user_foto' => $progress->user_foto
						? url('storage/foto/' . $progress->user_foto)
						: null,
					'form_statuses' => [
						'form_1' => $progress->form_1_status ?? 'Terkunci',
						'form_2' => $progress->form_2_status ?? 'Terkunci',
						'form_3' => $progress->form_3_status ?? 'Terkunci',
					]
				];
			});

			return response()->json([
				'status' => true,
				'message' => 'Berhasil mengambil data progres semua asesi untuk asesor ini.',
				'data' => $data
			]);
		} catch (\Exception $e) {
			return response()->json([
				'status' => false,
				'message' => 'Terjadi kesalahan saat mengambil data progres.',
				'data' => null,
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function getForm1ByAsesor(Request $request) 
	{
		$userId = $request->input('user_id');
		$status = $request->input('status'); // Tambahkan input status

		// Validasi input user_id
		if (!$userId) {
			return response()->json([
				'success' => "ERR",
				'message' => 'Parameter user_id wajib diisi.',
			], 400);
		}

		// Cek apakah user adalah asesor
		$asesor = DB::table('data_asesor')->where('user_id', $userId)->first();

		if (!$asesor) {
			return response()->json([
				'success' => "OK",
				'message' => 'User ini bukan asesor.',
			], 403); // Forbidden
		}

		// Ambil no_reg asesor
		$noReg = $asesor->no_reg;

		if (!$noReg) {
			return response()->json([
				'success' => "ERR",
				'message' => 'No Registrasi tidak ditemukan untuk asesor ini.',
			], 404);
		}

		// Query dasar
		$query = DB::table('form_1')->where('no_reg', $noReg);

		// Tambahkan filter status jika diberikan
		if (!is_null($status)) {
			$query->where('status', $status);
		}

		// Eksekusi query
		$formList = $query->orderBy('created_at', 'desc')->get();

		// Map foto berdasarkan user_id
		$formList = $formList->map(function ($item) {
			$user = DaftarUser::find($item->user_id);

			$item->foto = $user && $user->foto
				? url('storage/foto_nurse/' . basename($user->foto))
				: null;

			return $item;
		});

		return response()->json([
			'success' => "OK",
			'message' => 'Data form_1 berhasil diambil berdasarkan no_reg asesor.',
			'no_reg' => $noReg,
			'data' => $formList
		]);
	}


	public function getForm1ByAsesi(Request $request)
	{
		$userId = $request->input('user_id');
		$status = $request->input('status', 'Waiting'); // Default status ke "Waiting"

		// Validasi input user_id
		if (!$userId) {
			return response()->json([
				'success' => "ERR",
				'message' => 'Parameter user_id wajib diisi.',
			], 400);
		}

		// Cek apakah user terdaftar sebagai asesi
		$user = DB::table('users')->where('user_id', $userId)->first();

		if (!$user) {
			return response()->json([
				'success' => "ERR",
				'message' => 'User tidak ditemukan atau bukan asesi.',
			], 404);
		}

		// Ambil form_1 berdasarkan user_id dan status (default: Waiting)
		$query = DB::table('form_1')->where('user_id', $userId);

		if (!is_null($status)) {
			$query->where('status', $status);
		}

		$formList = $query->orderBy('created_at', 'desc')->get();

		return response()->json([
			'success' => "OK",
			'message' => 'Data form_1 berhasil diambil berdasarkan user_id asesi.',
			'user_id' => $userId,
			'status_filter' => $status,
			'data' => $formList
		]);
	}



}

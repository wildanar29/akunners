<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Service\OneSignalService;
use App\Service\FormService;
use App\Models\InterviewModel;
use App\Models\DataAsesorModel;
use App\Models\DaftarUser;
use App\Models\UserRole;
use App\Models\Form5;
use App\Models\BidangModel;
use App\Models\LangkahForm5;
use App\Models\KompetensiTrack;
use App\Models\KompetensiProgres;
use App\Models\Notification;
use App\Models\Form5KegiatanUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


class Form5Controller extends BaseController
{

	protected $oneSignalService;
	protected $formService;

    public function __construct(FormService $formService, OneSignalService $oneSignalService)
    {
        $this->formService = $formService;
        $this->oneSignalService = $oneSignalService;
    }

	private function kirimNotifikasiKeAsesor(DaftarUser $userAsesor, $formId)
	{
		if (empty($userAsesor->device_token)) {
			Log::warning("Asesor user_id={$userAsesor->user_id} tidak memiliki device_token.");
			return;
		}

		try {
			$title = 'Pengajuan baru';
			$message = "Anda memiliki Pengajuan konsultasi pra asesmen. cek disini.";

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

	private function kirimNotifikasiKeAsesorForm5(DaftarUser $userAsesor)
	{
		if (empty($userAsesor->device_token)) {
			Log::warning("Asesor user_id={$userAsesor->user_id} tidak memiliki device_token.");
			return;
		}

		try {
			$title = 'Form 5';
			$message = "Form 5 sudah disetujui oleh asesi.";

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
	
	public function pengajuanKonsultasiPraAsesmen(Request $request)
	{
		$user = auth()->user();

		if (!$user) {
			return response()->json([
				'status' => 401,
				'message' => 'Pengguna belum login atau token tidak valid.',
				'data' => []
			], 401);
		}

		// ✅ Cek role_id melalui model UserRole
		$isAsesi = UserRole::where('user_id', $user->user_id)
			->where('role_id', 1)
			->exists();

		if (!$isAsesi) {
			return response()->json([
				'status' => 403,
				'message' => 'Akses ditolak. Hanya asesi yang dapat melakukan pengajuan konsultasi.',
				'data' => []
			], 403);
		}

		// ✅ Validasi input (tambahkan pk_id)
		$this->validate($request, [
			'date' => 'required|date',
			'time' => 'required',
			'place' => 'required|string|max:255',
			'form_1_id' => 'required|integer|exists:form_1,form_1_id',
			'pk_id' => 'required|integer' // validasi pk_id
		]);

		// Cek apakah user sudah memiliki pengajuan untuk form_1_id ini
		$existing = InterviewModel::where('user_id', $user->user_id)
			->where('form_1_id', $request->form_1_id)
			->exists();

		if ($existing) {
			return response()->json([
				'status' => 409,
				'message' => 'Pengajuan untuk form ini sudah ada. Anda tidak dapat mengajukan dua kali.'
			]);
		}

		DB::beginTransaction();

		try {
			// Ambil data bidang / form_1
			$bidang = BidangModel::find($request->form_1_id);
			if (!$bidang) {
				return response()->json([
					'status' => 404,
					'message' => 'Data form_1 tidak ditemukan.'
				], 404);
			}

			// ✅ Validasi kecocokan pk_id dari form_1 dengan request
			if ($bidang->pk_id != $request->pk_id) {
				return response()->json([
					'status' => 400,
					'message' => 'Form yang dipilih tidak sesuai dengan program keahlian yang Anda ajukan.'
				], 400);
			}

			// Ambil data asesor
			$asesorData = DataAsesorModel::where('no_reg', $bidang->no_reg)->first();
			if (!$asesorData) {
				return response()->json([
					'status' => 404,
					'message' => 'Data asesor dengan no_reg tersebut tidak ditemukan.'
				]);
			}

			$userAsesor = DaftarUser::where('user_id', $asesorData->user_id)->first();
			$asesorName = $userAsesor ? $userAsesor->nama : 'Nama tidak tersedia';

			// Simpan ke InterviewModel
			$interview = new InterviewModel();
			$interview->asesi_name = $user->nama;
			$interview->user_id = $user->user_id;
			$interview->date = $request->date;
			$interview->time = $request->time;
			$interview->place = $request->place;
			$interview->form_1_id = $request->form_1_id;
			$interview->asesor_id = $asesorData->user_id;
			$interview->asesor_name = $asesorName;
			$interview->status = 'Waiting';
			$interview->pk_id = $request->pk_id;

			$interview->save();

			if ($userAsesor) {
				$this->kirimNotifikasiKeAsesor($userAsesor, $request->form_1_id);
			}
			DB::commit();

			$progres = KompetensiProgres::create([
				'form_id' => $interview->interview_id,
				'parent_form_id' => $request->form_1_id,
				'user_id' => $user->user_id,
				'status' => 'Submitted',
			]);

			KompetensiTrack::create([
				'progres_id' => $progres->id,
				'form_type' => 'intv_pra_asesmen',
				'activity' => 'Submitted',
				'activity_time' => Carbon::now(),
				'description' => 'Asesi mengajukan konsultasi pra asesmen.',
			]);

			return response()->json([
				'status' => 201,
				'message' => 'Pengajuan konsultasi pra asesmen berhasil disimpan.',
				'data' => $interview
			], 201);
		} catch (\Exception $e) {
			DB::rollBack();

			return response()->json([
				'status' => 500,
				'message' => 'Terjadi kesalahan saat menyimpan pengajuan konsultasi.',
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function getJadwalInterviewGabungan(Request $request)
	{
		$user = Auth::user();

		if (!$user) {
			return response()->json([
				'status' => 401,
				'message' => 'User belum login.',
				'data' => []
			], 401);
		}

		// Deteksi role
		$userId   = $user->user_id;
		$isBidang = UserRole::where('user_id', $userId)->where('role_id', 3)->exists();
		$isAsesor = DB::table('data_asesor')->where('user_id', $userId)->exists();

		if (!$isBidang && !$isAsesor) {
			return response()->json([
				'status' => 403,
				'message' => 'Akses ditolak. Hanya Bidang atau Asesor yang dapat mengakses.',
				'data' => []
			], 403);
		}

		// Tangkap input filter
		$date       = $request->input('date');
		$time       = $request->input('time');
		$place      = $request->input('place');
		$status     = $request->input('status');
		$pk_id      = $request->input('pk_id');
		$asesorId   = $request->input('asesor_id');
		$noReg      = $request->input('no_reg');

		// Mulai query
		$query = DB::table('schedule_interview as si')
			->join('form_1 as f1', 'si.form_1_id', '=', 'f1.form_1_id')
			->select('si.*');

		// === JIKA BIDANG ===
		if ($isBidang && !$isAsesor) {
			if (!empty($date)) {
				$query->whereDate('si.date', $date);
			}
			if (!empty($time)) {
				$query->where('si.time', $time);
			}
			if (!empty($place)) {
				$query->where('si.place', 'like', '%' . $place . '%');
			}
			if (!empty($asesorId)) {
				$query->where('si.asesor_id', $asesorId);
			}
			if (!empty($status)) {
				$query->where('si.status', $status);
			}
		}

		// === JIKA ASESOR ===
		elseif ($isAsesor) {
			if ($asesorId) {
				$asesor = DB::table('data_asesor')->where('user_id', $asesorId)->first();
				if (!$asesor) {
					return response()->json([
						'status' => 404,
						'message' => 'Asesor tidak ditemukan berdasarkan asesor_id.',
						'data' => []
					], 404);
				}
			} elseif ($noReg) {
				$asesor = DB::table('data_asesor')->where('no_reg', $noReg)->first();
				if (!$asesor) {
					return response()->json([
						'status' => 404,
						'message' => 'Asesor tidak ditemukan berdasarkan no_reg.',
						'data' => []
					], 404);
				}
				$asesorId = $asesor->user_id;
			} else {
				$asesorId = $user->user_id;
			}

			$query->where('si.asesor_id', $asesorId)
				->where('si.status', 'waiting');

			if ($pk_id) {
				$query->where('f1.pk_id', $pk_id);
			}
		}

		// Ambil data dan urutkan
		$jadwal = $query->orderBy('si.date', 'asc')
						->orderBy('si.time', 'asc')
						->get();

		return response()->json([
			'status' => 200,
			'message' => 'Data jadwal interview berhasil diambil.',
			'data' => $jadwal
		]);
	}

	private function kirimNotifikasiApprovalKePengaju($formData)
	{
		if (!$formData || empty($formData->user_id)) {
			Log::warning('Gagal kirim notifikasi: user_id pengaju kosong');
			return;
		}

		$pengaju = DaftarUser::where('user_id', $formData->user_id)->first();

		if (!$pengaju) {
			Log::warning('User pengaju tidak ditemukan', ['user_id' => $formData->user_id]);
			return;
		}

		if (empty($pengaju->device_token)) {
			Log::warning("User pengaju tidak memiliki device_token", ['user_id' => $pengaju->user_id]);
			return;
		}

		try {
			$title = 'Pengajuan Disetujui';
			$message = "Pengajuan konsultasi pra asesmen disetujui.";

			// Kirim notifikasi ke OneSignal
			$this->oneSignalService->sendNotification(
				[$pengaju->device_token],
				$title,
				$message
			);

			Log::info('Notifikasi berhasil dikirim ke pengaju', [
				'user_id' => $pengaju->user_id,
				'nama' => $pengaju->nama ?? null,
			]);

			// Simpan ke tabel notification
			Notification::create([
				'user_id' => $pengaju->user_id,
				'title' => $title,
				'description' => $message,
				'is_read' => 0,
				'created_at' => Carbon::now(),
				'updated_at' => Carbon::now(),
			]);

		} catch (\Exception $e) {
			Log::error('Gagal kirim notifikasi ke pengaju', [
				'user_id' => $pengaju->user_id,
				'error' => $e->getMessage(),
			]);
		}
	}

	private function kirimNotifikasiApprovalKeAsesiForm5($formData)
	{
		if (!$formData || empty($formData->user_id)) {
			Log::warning('Gagal kirim notifikasi: user_id pengaju kosong');
			return;
		}

		$pengaju = DaftarUser::where('user_id', $formData->user_id)->first();

		if (!$pengaju) {
			Log::warning('User pengaju tidak ditemukan', ['user_id' => $formData->user_id]);
			return;
		}

		if (empty($pengaju->device_token)) {
			Log::warning("User pengaju tidak memiliki device_token", ['user_id' => $pengaju->user_id]);
			return;
		}

		try {
			$title = 'Form 5';
			$message = "Silahkan baca dan setujui Form 5.";

			// Kirim notifikasi ke OneSignal
			$this->oneSignalService->sendNotification(
				[$pengaju->device_token],
				$title,
				$message
			);

			Log::info('Notifikasi berhasil dikirim ke pengaju', [
				'user_id' => $pengaju->user_id,
				'nama' => $pengaju->nama ?? null,
			]);

			// Simpan ke tabel notification
			Notification::create([
				'user_id' => $pengaju->user_id,
				'title' => $title,
				'description' => $message,
				'is_read' => 0,
				'created_at' => Carbon::now(),
				'updated_at' => Carbon::now(),
			]);

		} catch (\Exception $e) {
			Log::error('Gagal kirim notifikasi ke pengaju', [
				'user_id' => $pengaju->user_id,
				'error' => $e->getMessage(),
			]);
		}
	}

	public function updateStatusInterview(Request $request)
	{
		$this->validate($request, [
			'interview_id' => 'required|integer|exists:schedule_interview,interview_id',
			'action' => 'required|in:Approved,Rejected,Reschedule',
			'date' => 'required_if:action,Reschedule|date',
			'time' => 'required_if:action,Reschedule',
			'place' => 'required_if:action,Reschedule|string|max:255',
			'asesor_id' => 'nullable|integer|exists:data_asesor,user_id'
		]);

		$user = Auth::user();
		$asesorId = $request->input('asesor_id', $user?->user_id);

		if (!$asesorId) {
			return response()->json([
				'status' => 401,
				'message' => 'User tidak terautentikasi dan asesor_id tidak disediakan.',
				'data' => []
			], 401);
		}

		$asesor = DB::table('data_asesor')->where('user_id', $asesorId)->first();
		if (!$asesor) {
			return response()->json([
				'status' => 403,
				'message' => 'Akses ditolak. User bukan asesor.',
				'data' => []
			], 403);
		}

		$interview = DB::table('schedule_interview')->where('interview_id', $request->interview_id)->first();
		if (!$interview) {
			return response()->json([
				'status' => 404,
				'message' => 'Data interview tidak ditemukan.',
				'data' => []
			], 404);
		}

		if ($interview->asesor_id != $asesorId) {
			return response()->json([
				'status' => 403,
				'message' => 'Akses ditolak. Anda bukan pemilik jadwal interview ini.',
				'data' => []
			], 403);
		}

		$updateData = [];

		switch ($request->action) {
			case 'Approved':
				$updateData['status'] = 'Approved';
				break;
			case 'Rejected':
				$updateData['status'] = 'Rejected';
				break;
			case 'Reschedule':
				$updateData['status'] = 'Rescheduled';
				$updateData['date'] = $request->date;
				$updateData['time'] = $request->time;
				$updateData['place'] = $request->place;
				break;
		}

		DB::table('schedule_interview')
			->where('interview_id', $request->interview_id)
			->update($updateData);

		// Update or Create KompetensiProgres
		$progres = KompetensiProgres::firstOrCreate(
			['form_id' => $request->interview_id],
			['status' => 'Approved']
		);
		$progres->status = 'Approved';
		$progres->save();

		// Tambahkan track untuk interview approval
		KompetensiTrack::create([
			'progres_id' => $progres->id,
			'form_type' => 'intv_pra_asesmen',
			'activity' => 'Approved',
			'activity_time' => Carbon::now(),
			'description' => 'Pengajuan konsultasi pra asesmen disetujui oleh Asesor.',
		]);

		// Proses Form5
		$form5Result = $this->initForm5($user?->user_id, $request->asesor_id);

		if (!$form5Result || !isset($form5Result['form5'])) {
			return response()->json([
				'status' => 'FAILED',
				'message' => 'Form5 gagal dibuat.',
			], 500);
		}

		$form5 = $form5Result['form5'];

		// Hanya proses jika Form5 baru dibuat
		if (!$form5Result['exists']) {
			$this->initJawabanKosongForm5($form5->form_5_id, $interview->pk_id ?? null);
			$this->kirimNotifikasiApprovalKePengaju($interview);

			$newProgres = KompetensiProgres::create([
				'form_id' => $form5->form_5_id,
				'parent_form_id' => $progres->parent_form_id ?? null,
				'user_id' => $form5->asesi_id,
				'status' => 'InAssessment',
			]);

			KompetensiTrack::create([
				'progres_id' => $newProgres->id,
				'form_type' => 'form_5',
				'activity' => 'InAssessment',
				'activity_time' => Carbon::now(),
				'description' => 'Formulir 5 berhasil dibuat dan proses asesmen dimulai.',
			]);
		}

		return response()->json([
			'status' => "SUCCESS",
			'message' => $form5Result['message'] ?? 'Status interview berhasil diperbarui.',
			'data' => $updateData,
			'form5_exists' => $form5Result['exists'],
			'form_5_id' => $form5->form_5_id
		]);
	}

	private function initForm5(?int $userId, ?int $asesorId)
	{
		$finalAsesorId = $asesorId ?? $userId;

		if (!$finalAsesorId) {
			Log::warning('Validasi asesor gagal: user_id dan asesor_id keduanya null.');
			return false;
		}

		$asesor = DB::table('data_asesor')->where('user_id', $finalAsesorId)->first();
		if (!$asesor) {
			Log::warning('Validasi asesor gagal. Data tidak ditemukan di tabel data_asesor.', [
				'user_id' => $finalAsesorId
			]);
			return false;
		}

		$asesorUser = DaftarUser::where('user_id', $finalAsesorId)->first();
		if (!$asesorUser) {
			Log::warning('Data user untuk asesor tidak ditemukan di DaftarUser.', [
				'user_id' => $finalAsesorId
			]);
			return false;
		}

		$asesi = DaftarUser::where('user_id', $userId)->first();
		if (!$asesi) {
			Log::warning('Data asesi tidak ditemukan.', [
				'asesi_id' => $userId
			]);
			return false;
		}

		$form5Exist = Form5::where('asesi_id', $userId)
			->where('status', '!=', 'Cancel')
			->first();

		if ($form5Exist) {
			Log::info('Form5 sudah ada sebelumnya.', ['form_5_id' => $form5Exist->form_5_id]);
			return [
				'message' => 'Form5 sudah ada sebelumnya.',
				'exists' => true,
				'form5' => $form5Exist,
			];
		}

		try {
			$form5 = Form5::create([
				'asesi_id'     => $userId,
				'asesi_name'   => $asesi->nama ?? '',
				'asesi_date'   => Carbon::now()->toDateString(),
				'asesor_id'    => $asesor->user_id,
				'asesor_name'  => $asesorUser->nama ?? '',
				'asesor_date'  => Carbon::now()->toDateString(),
				'no_reg'       => $asesor->no_reg ?? '',
				'status'       => 'InAssessment',
			]);

			Log::info('Form5 berhasil dibuat.', ['form_5_id' => $form5->form_5_id]);

			return [
				'message' => 'Form5 berhasil dibuat.',
				'exists' => false,
				'form5' => $form5,
			];
		} catch (\Exception $e) {
			Log::error('Gagal menyimpan Form5.', ['error' => $e->getMessage()]);
			return false;
		}
	}

	public function getLangkahDanKegiatan(Request $request)
	{
		try {
			$pk_id = $request->input('pk_id');

			if (!$pk_id) {
				return response()->json([
					'status' => 400,
					'message' => 'Parameter pk_id wajib diisi.',
					'data' => [],
				], 400);
			}

			$data = LangkahForm5::with('kegiatans')
				->where('pk_id', $pk_id)
				->orderBy('nomor_langkah')
				->get();

			return response()->json([
				'status' => 200,
				'message' => 'Data langkah dan kegiatan berhasil diambil.',
				'data' => $data
			]);
		} catch (\Exception $e) {
			Log::error('Gagal mengambil data langkah dan kegiatan: ' . $e->getMessage());

			return response()->json([
				'status' => 500,
				'message' => 'Terjadi kesalahan saat mengambil data.',
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function simpanJawabanKegiatan(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'jawaban' => 'required|array|min:1',
			'jawaban.*.form_5_id'   => 'required|integer|exists:form_5,form_5_id',
			'jawaban.*.kegiatan_id' => 'required|integer|exists:kegiatan_form5,id',
			'jawaban.*.is_tercapai' => 'required|boolean',
			'jawaban.*.catatan'     => 'nullable|string|max:1000'
		]);

		if ($validator->fails()) {
			return response()->json([
				'status' => 422,
				'message' => 'Validasi gagal.',
				'errors' => $validator->errors()
			], 422);
		}

		try {
			$data = $request->input('jawaban');
			$form5Id = $data[0]['form_5_id']; // Ambil satu form_5_id dari jawaban

			// Simpan atau update jawaban kegiatan
			foreach ($data as $item) {
				Form5KegiatanUser::updateOrCreate(
					[
						'form_5_id'   => $item['form_5_id'],
						'kegiatan_id' => $item['kegiatan_id']
					],
					[
						'is_tercapai' => (int) $item['is_tercapai'],
						'catatan'     => $item['catatan'] ?? null,
						'updated_at'  => Carbon::now()
					]
				);
			}

			// Ambil data Form 5 dan relasi ke KompetensiProgres
			$form5 = Form5::find($form5Id);

			// $progres = KompetensiProgres::where('form_id', $form5->form_5_id)->first();
			if ($form5) {
				Log::info('masuk ke update status Form5', ['form_5_id' => $form5Id]);

				// Ambil data progres dulu
				$progres = KompetensiProgres::where('form_id', $form5Id)->first();

				if ($progres) {
					// Update status
					$progres->update([
						'status' => 'Submitted',
						'updated_at' => Carbon::now()
					]);

					// Tambahkan ke track
					KompetensiTrack::create([
						'progres_id'  => $progres->id,
						'status'      => 'Submitted',
						'form_type'   => 'form_5',
						'description' => 'Asesor selesai mengisi Form 5',
						'created_at'  => Carbon::now()
					]);
				}
			}

			$this->kirimNotifikasiApprovalKeAsesiForm5($progres);
			return response()->json([
				'status' => 200,
				'message' => 'Jawaban kegiatan berhasil disimpan dan status diperbarui.'
			]);
		} catch (\Exception $e) {
			Log::error('Gagal menyimpan jawaban kegiatan: ' . $e->getMessage());

			return response()->json([
				'status' => 500,
				'message' => 'Terjadi kesalahan saat menyimpan data.',
				'error' => $e->getMessage()
			], 500);
		}
	}

	private function initJawabanKosongForm5(int $form5Id, ?int $pkId = null)
	{
		$query = DB::table('kegiatan_form5')->select('id');
		if ($pkId) {
			$query->where('pk_id', $pkId);
		}

		$kegiatanList = $query->get();

		foreach ($kegiatanList as $kegiatan) {
			Form5KegiatanUser::updateOrCreate(
				[
					'form_5_id'   => $form5Id,
					'kegiatan_id' => $kegiatan->id,
				],
				[
					'is_tercapai' => 0,
					'catatan'     => null,
					'updated_at'  => Carbon::now(),
				]
			);
		}
	}

	public function getLangkahKegiatanDenganJawaban(Request $request)
	{
		try {
			$pk_id = $request->input('pk_id');
			$form5_id = $request->input('form_5_id');

			if (!$pk_id || !$form5_id) {
				return response()->json([
					'status' => 400,
					'message' => 'Parameter pk_id dan form_5_id wajib diisi.',
					'data' => [],
				], 400);
			}

			// Ambil semua jawaban user untuk form_5_id
			$jawabanUser = Form5KegiatanUser::where('form_5_id', $form5_id)->get()
				->keyBy('kegiatan_id'); // key untuk mempermudah pencocokan

			// Ambil langkah beserta kegiatan
			$data = LangkahForm5::with(['kegiatans' => function ($query) use ($jawabanUser) {
				$query->orderBy('created_at', 'asc');
			}])
				->where('pk_id', $pk_id)
				->orderBy('created_at', 'asc')
				->get();

			// Tambahkan jawaban ke setiap kegiatan
			$data->transform(function ($langkah) use ($jawabanUser) {
				$langkah->kegiatans->transform(function ($kegiatan) use ($jawabanUser) {
					$jawaban = $jawabanUser->get($kegiatan->id);

					$kegiatan->is_tercapai = $jawaban->is_tercapai ?? null;
					$kegiatan->catatan = $jawaban->catatan ?? null;

					return $kegiatan;
				});
				return $langkah;
			});

			return response()->json([
				'status' => 200,
				'message' => 'Data langkah, kegiatan, dan jawaban berhasil diambil.',
				'data' => $data
			]);
		} catch (\Exception $e) {
			Log::error('Gagal mengambil data langkah + jawaban: ' . $e->getMessage());

			return response()->json([
				'status' => 500,
				'message' => 'Terjadi kesalahan saat mengambil data.',
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function approveKompetensiProgres(Request $request)
	{
		try {
			$formId = $request->input('form_id');
			$pk_id = $request->input('pk_id');

			if (!$formId) {
				Log::warning('approveKompetensiProgres: form_id tidak ditemukan dalam request.');
				return response()->json([
					'status' => 400,
					'message' => 'Parameter form_id wajib diisi.',
				], 400);
			}

			$query = KompetensiProgres::where('form_id', $formId);
			if ($pk_id) {
				$query->where('pk_id', $pk_id);
			}

			$progres = $query->first();
			if (!$progres) {
				Log::warning("approveKompetensiProgres: Data progres tidak ditemukan untuk form_id={$formId}, pk_id={$pk_id}");
				return response()->json([
					'status' => 404,
					'message' => 'Data KompetensiProgres tidak ditemukan.',
				], 404);
			}

			$progres->status = 'Approved';
			$progres->updated_at = Carbon::now();
			$progres->save();

			Log::info("Status KompetensiProgres updated ke Approved", [
				'form_id' => $formId,
				'pk_id' => $pk_id
			]);

			// TRACKING
			try {
				KompetensiTrack::create([
					'progres_id' => $progres->id,
					'form_type' => 'form_5',
					'activity' => 'Approved',
					'activity_time' => Carbon::now(),
					'description' => 'Form 5 telah disetujui oleh asesi.',
				]);
			} catch (\Exception $e) {
				Log::error("Gagal membuat KompetensiTrack: " . $e->getMessage());
			}

			// NOTIFIKASI
			try {
				$form5 = Form5::find($formId);
				$asesor_id = $form5->asesor_id ?? null;
				if ($asesor_id) {
					Log::info("Kirim notifikasi ke asesor user_id={$asesor_id} untuk form_5 id={$formId}");
					$asesor = DaftarUser::where('user_id', $asesor_id)->first();
					if ($asesor) {
						$this->kirimNotifikasiKeAsesorForm5($asesor);
					} else {
						Log::warning("Asesor dengan user_id={$asesor_id} tidak ditemukan di tabel DaftarUser.");
					}
				}
			} catch (\Exception $e) {
				Log::error("Gagal mengirim notifikasi ke asesor: " . $e->getMessage());
			}

			// HANDLE FORM6
			try {
				$form1Id = $this->formService->getParentFormIdByFormId($formId);
				$form1Data = $this->formService->getParentDataByFormId($form1Id);

				if (!$form1Data) {
					Log::warning("Data Form1 tidak ditemukan dengan form_1_id={$form1Id}");
				} else {
					Log::info('Cek FormService Data', [
						'form1Id' => $form1Id,
						'asesi_id' => $form1Data->asesi_id ?? null,
						'pk_id' => $form1Data->pk_id ?? null
					]);

					$isForm6Exist = $this->formService->isFormExist(
						$form1Data->asesi_id,
						$form1Data->pk_id,
						'form_6'
					);

					Log::info('Hasil Cek isForm6Exist', [
						'isForm6Exist' => $isForm6Exist
					]);

					if (!$isForm6Exist) {
						Log::info("Form 6 belum ada, membuat form 6...");
						$form6 = $this->formService->inputForm6(
							$form1Data->pk_id,
							$form1Data->asesi_id,
							$form1Data->asesi_name,
							$form1Data->asesor_id,
							$form1Data->asesor_name,
							$form1Data->no_reg
						);

						$this->formService->createProgresDanTrack($form6->form_6_id, 'form_6', 'InAssessment', $form1Data->asesi_id, $form1Data->form_1_id, 'Form 6 sudah dapat diisi.');
					} else {
						Log::info("Form 6 sudah ada, tidak membuat ulang.");
					}
				}
			} catch (\Exception $e) {
				Log::error("Gagal cek atau input form_6: " . $e->getMessage());
			}

			return response()->json([
				'status' => 200,
				'message' => 'Status KompetensiProgres berhasil diupdate ke Approved.'
			]);

		} catch (\Exception $e) {
			Log::error('Gagal update status KompetensiProgres (main try/catch): ' . $e->getMessage(), [
				'trace' => $e->getTraceAsString()
			]);

			return response()->json([
				'status' => 500,
				'message' => 'Terjadi kesalahan saat update status.',
				'error' => $e->getMessage()
			], 500);
		}
	}

}

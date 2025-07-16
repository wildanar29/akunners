<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\InterviewModel;
use App\Models\DataAsesorModel;
use App\Models\DaftarUser;
use App\Models\BidangModel;
use App\Models\LangkahForm5;
use App\Models\Form5KegiatanUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon; // Tambahkan ini untuk menggunakan Carbon


class Form5Controller extends BaseController
{
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

		// Cek apakah user memiliki role Asesi (role_id = 1)
		$isAsesi = $user->roles()->where('role_id', 1)->exists();
		if (!$isAsesi) {
			return response()->json([
				'status' => 403,
				'message' => 'Akses ditolak. Hanya asesi yang dapat melakukan pengajuan konsultasi.',
				'data' => []
			], 403);
		}

		// Validasi input dari asesi
		$this->validate($request, [
			'date' => 'required|date',
			'time' => 'required',
			'place' => 'required|string|max:255',
			'form_1_id' => 'required|integer|exists:form_1,form_1_id'
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

		// Ambil no_reg dari form_1 (BidangModel)
		$bidang = BidangModel::find($request->form_1_id);
		if (!$bidang) {
			return response()->json([
				'status' => 404,
				'message' => 'Data form_1 tidak ditemukan.'
			], 404);
		}

		// Ambil data asesor dari DataAsesorModel berdasarkan no_reg
		$asesorData = DataAsesorModel::where('no_reg', $bidang->no_reg)->first();

		if (!$asesorData) {
			return response()->json([
				'status' => 404,
				'message' => 'Data asesor dengan no_reg tersebut tidak ditemukan.'
			]);
		}

		// Ambil nama asesor dari tabel users
		$userAsesor = DaftarUser::where('user_id', $asesorData->user_id)->first();
		$asesorName = $userAsesor ? $userAsesor->nama : 'Nama tidak tersedia';

		// Simpan data ke InterviewModel
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
		$interview->save();

		return response()->json([
			'status' => 201,
			'message' => 'Pengajuan konsultasi pra asesmen berhasil disimpan.',
			'data' => $interview
		], 201);
	}

	
	public function getJadwalInterviewByAsesor(Request $request)
	{
		$user = Auth::user();

		// Cek user login
		if (!$user) {
			return response()->json([
				'status' => 401,
				'message' => 'User tidak terautentikasi.',
				'data' => []
			], 401);
		}

		// Tangkap inputan
		$inputAsesorId = $request->input('asesor_id');
		$inputNoReg = $request->input('no_reg');

		$asesorId = null;

		// Jika ada inputan asesor_id langsung digunakan
		if ($inputAsesorId) {
			$asesor = DB::table('data_asesor')->where('user_id', $inputAsesorId)->first();
			if (!$asesor) {
				return response()->json([
					'status' => 404,
					'message' => 'Asesor tidak ditemukan berdasarkan asesor_id.',
					'data' => []
				], 404);
			}
			$asesorId = $inputAsesorId;
		}
		// Jika ada inputan no_reg, cari user_id asesor dari data_asesor
		elseif ($inputNoReg) {
			$asesor = DB::table('data_asesor')->where('no_reg', $inputNoReg)->first();
			if (!$asesor) {
				return response()->json([
					'status' => 404,
					'message' => 'Asesor tidak ditemukan berdasarkan no_reg.',
					'data' => []
				], 404);
			}
			$asesorId = $asesor->user_id;
		}
		// Kalau tidak ada input, pakai user yang login
		else {
			$asesor = DB::table('data_asesor')->where('user_id', $user->user_id)->first();
			if (!$asesor) {
				return response()->json([
					'status' => 403,
					'message' => 'Akses ditolak. Hanya asesor yang dapat melihat jadwal ini.',
					'data' => []
				], 403);
			}
			$asesorId = $user->user_id;
		}

		// Ambil jadwal interview
		$jadwal = DB::table('schedule_interview')
			->where('asesor_id', $asesorId)
			->orderBy('date', 'asc')
			->orderBy('time', 'asc')
			->get();

		return response()->json([
			'status' => 200,
			'message' => 'Data jadwal interview berhasil diambil.',
			'data' => $jadwal
		]);
	}

	public function updateStatusInterview(Request $request)
	{
		$this->validate($request, [
			'interview_id' => 'required|integer|exists:schedule_interview,interview_id',
			'action' => 'required|in:accepted,canceled,reschedule',
			'date' => 'required_if:action,reschedule|date',
			'time' => 'required_if:action,reschedule',
			'place' => 'required_if:action,reschedule|string|max:255',
			'asesor_id' => 'nullable|integer|exists:data_asesor,user_id' // opsional, valid jika ada
		]);

		$user = Auth::user();

		// Ambil ID asesor dari login atau dari request
		$asesorId = $request->input('asesor_id', $user?->user_id);

		// Validasi keberadaan user (jika tidak login dan tidak ada asesor_id dikirim)
		if (!$asesorId) {
			return response()->json([
				'status' => 401,
				'message' => 'User tidak terautentikasi dan asesor_id tidak disediakan.',
				'data' => []
			], 401);
		}

		// Cek apakah asesor_id valid di tabel data_asesor
		$asesor = DB::table('data_asesor')->where('user_id', $asesorId)->first();
		if (!$asesor) {
			return response()->json([
				'status' => 403,
				'message' => 'Akses ditolak. User bukan asesor.',
				'data' => []
			], 403);
		}

		// Ambil data interview
		$interview = DB::table('schedule_interview')->where('interview_id', $request->interview_id)->first();
		if (!$interview) {
			return response()->json([
				'status' => 404,
				'message' => 'Data interview tidak ditemukan.',
				'data' => []
			], 404);
		}

		// Cek apakah interview dimiliki oleh asesor_id yang sedang login atau dikirimkan
		if ($interview->asesor_id != $asesorId) {
			return response()->json([
				'status' => 403,
				'message' => 'Akses ditolak. Anda bukan pemilik jadwal interview ini.',
				'data' => []
			], 403);
		}

		// Proses update berdasarkan action
		$updateData = [];

		switch ($request->action) {
			case 'accepted':
				$updateData['status'] = 'Accepted';
				break;

			case 'canceled':
				$updateData['status'] = 'Canceled';
				break;

			case 'reschedule':
				$updateData['status'] = 'Rescheduled';
				$updateData['date'] = $request->date;
				$updateData['time'] = $request->time;
				$updateData['place'] = $request->place;
				break;
		}

		DB::table('schedule_interview')
			->where('interview_id', $request->interview_id)
			->update($updateData);

		return response()->json([
			'status' => 200,
			'message' => 'Status interview berhasil diperbarui.',
			'data' => $updateData
		]);
	}
	
	public function getJadwalInterviewByBidang(Request $request)
	{
		$authUser = auth()->user();

		if (!$authUser) {
			return response()->json([
				'status' => 401,
				'message' => 'User belum login.',
				'data' => []
			], 401);
		}

		// Cek apakah user memiliki role Bidang (role_id = 3)
		$isBidang = $authUser->roles()->where('role_id', 3)->exists();
		if (!$isBidang) {
			return response()->json([
				'status' => 403,
				'message' => 'Akses ditolak. Hanya user dengan role Bidang yang dapat mengakses.',
				'data' => []
			], 403);
		}

		// Ambil parameter filter dari input
		$date     = $request->input('date');
		$time     = $request->input('time');
		$place    = $request->input('place');
		$asesorId = $request->input('asesor_id');
		$status   = $request->input('status');

		// Query data dari schedule_interview
		$query = DB::table('schedule_interview');

		// Filter berdasarkan input jika ada
		if (!empty($date)) {
			$query->whereDate('date', $date);
		}
		if (!empty($time)) {
			$query->where('time', $time);
		}
		if (!empty($place)) {
			$query->where('place', 'like', '%' . $place . '%');
		}
		if (!empty($asesorId)) {
			$query->where('asesor_id', $asesorId);
		}
		if (!empty($status)) {
			$query->where('status', $status);
		}

		// Ambil data dan urutkan
		$jadwal = $query->orderBy('date')
						->orderBy('time')
						->get();

		return response()->json([
			'status' => 200,
			'message' => 'Data jadwal interview berhasil diambil.',
			'data' => $jadwal
		]);
	}

	
	public function getLangkahDanKegiatan()
	{
		try {
			$data = LangkahForm5::with(['kegiatans'])
				->orderBy('nomor_langkah')
				->get();

			return response()->json([
				'status' => 200,
				'message' => 'Data langkah dan kegiatan berhasil diambil.',
				'data' => $data
			]);
		} catch (\Exception $e) {
			// Log kesalahan ke file log Laravel
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
		// Validasi format data 'jawaban'
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

			foreach ($data as $item) {
				Form5KegiatanUser::updateOrCreate(
					[
						'form_5_id'   => $item['form_5_id'],
						'kegiatan_id' => $item['kegiatan_id']
					],
					[
						'is_tercapai' => (int) $item['is_tercapai'], // Konversi boolean ke 0/1
						'catatan'     => $item['catatan'] ?? null,
						'updated_at'  => now()
					]
				);
			}

			return response()->json([
				'status' => 200,
				'message' => 'Jawaban kegiatan berhasil disimpan.'
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
}

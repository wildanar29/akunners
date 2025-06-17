<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\InterviewModel;
use App\Models\DataAsesorModel;
use App\Models\DaftarUser;
use App\Models\BidangModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon; // Tambahkan ini untuk menggunakan Carbon


class Form5Controller extends BaseController
{
	public function pengajuanKonsultasiPraAsesmen(Request $request)
	{
		$user = Auth::user();

		 if (!$user) {
			return response()->json([
				'status' => 401,
				'message' => 'Pengguna belum login atau token tidak valid.',
				'data' => []
			], 401);
		}

		// Cek jika bukan asesi (role_id != 1)
		if ($user->role_id != 1) {
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

		// Ambil data BidangModel
		$bidang = BidangModel::find($request->form_1_id);
		if (!$bidang || !$bidang->no_reg) {
			return response()->json([
				'status' => 404,
				'message' => 'Data bidang atau no_reg tidak ditemukan.'
			]);
		}

		// Ambil data asesor dari DataAsesorModel
		$asesorData = DataAsesorModel::where('no_reg', $bidang->no_reg)->first();

		if (!$asesorData) {
			return response()->json([
				'status' => 404,
				'message' => 'Data asesor dengan no_reg tersebut tidak ditemukan.'
			]);
		}

		// Ambil nama asesor dari DaftarUser berdasarkan user_id
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
		$interview->status = 'Waiting'; // <-- Tambahan di sini
		$interview->save();

		return response()->json([
			'status' => 201,
			'message' => 'Pengajuan konsultasi pra asesmen berhasil disimpan.',
			'data' => $interview
		]);
	}
}

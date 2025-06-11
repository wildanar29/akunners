<?php  
  
namespace App\Http\Controllers;  
  
use Illuminate\Http\Request;  
use App\Models\BidangModel;  
use App\Models\HistoryJabatan;
use App\Models\DaftarUser; // Pastikan untuk mengimpor model User  
use App\Models\IjazahModel; // Model untuk users_ijazah_file  
use App\Models\TranskripModel; // Model untuk users_transkrip_file  
use App\Models\SipModel; // Model untuk users_sip_file  
use App\Models\StrModel; // Model untuk users_str_file  
use App\Models\UjikomModel; // Model untuk users_str_file  
use App\Models\DataAsesorModel; // Model untuk users_str_file  
use App\Models\PkProgressModel; // Model untuk users_str_file  
use App\Models\PkStatusModel; // Model untuk users_str_file  
use App\Models\User; // Model untuk user  
use Illuminate\Support\Facades\Validator;  
use Illuminate\Support\Facades\DB; // Tambahkan DB untuk query manual
use Carbon\Carbon; // Pastikan untuk mengimpor Carbon  

  
class AsesorController extends Controller  
{  
/**
 * @OA\Get(
 *     path="/form1/asesor/{asesorName}",
 *     summary="Ambil data Form 1 berdasarkan nama asesor",
 *     tags={"Asesor"},
 *     @OA\Parameter(
 *         name="asesorName",
 *         in="path",
 *         description="Nama asesor untuk filter data",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data form_1 berhasil diambil",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Data form_1 berdasarkan asesor_name berhasil diambil."),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="asesor_name", type="string", example="John Doe"),
 *                     @OA\Property(property="field_name", type="string", example="Isi Field Lainnya")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data tidak ditemukan"
 *     )
 * )
 */

	public function getForm1ByAsesorName($asesorName)
	{
		// Ambil semua data dari form_1 berdasarkan nama asesor dan status = 'Waiting'
		$form1Data = DB::table('form_1')
			->where('asesor_name', $asesorName)
			->where('status', 'Waiting')
			->get();

		// Return JSON response
		return response()->json([
			'status' => 200,
			'message' => 'Data form_1 berdasarkan asesor_name dan status Waiting berhasil diambil.',
			'data' => $form1Data
		]);
	}
	
	    /**
     * @OA\Put(
     *     path="/form1/approve/{form_1_id}",
     *     summary="Setujui (approve) Form 1 berdasarkan ID",
     *     tags={"Asesor"},
     *     @OA\Parameter(
     *         name="form_1_id",
     *         in="path",
     *         description="ID Form 1 yang akan disetujui",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Status form_1 berhasil diperbarui menjadi Approved",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Status form_1 berhasil diperbarui menjadi Approved.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Data form_1 tidak ditemukan atau status bukan Waiting",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="message", type="string", example="Data form_1 tidak ditemukan atau status bukan Waiting.")
     *         )
     *     )
     * )
     */

	public function approveForm1ById($form_1_id)
	{
		// Cari data form_1 berdasarkan ID dan status Waiting
		$form = DB::table('form_1')
			->where('form_1_id', $form_1_id)
			->where('status', 'Waiting')
			->first();

		// Jika data tidak ditemukan atau status bukan Waiting
		if (!$form) {
			return response()->json([
				'status' => 404,
				'message' => 'Data form_1 tidak ditemukan atau status bukan Waiting.'
			]);
		}

		// Lakukan update status menjadi Approved
		DB::table('form_1')
			->where('form_1_id', $form_1_id)
			->update(['status' => 'Approved']);

		return response()->json([
			'status' => 200,
			'message' => 'Status form_1 berhasil diperbarui menjadi Approved.'
		]);
	}


	/**
	 * @OA\Post(
	 *     path="/jawaban-form2/update/{no_id}",
	 *     summary="Update k_asesor dan bk_asesor berdasarkan no_id",
	 *     tags={"Asesor"},
	 *     @OA\Parameter(
	 *         name="no_id",
	 *         in="path",
	 *         description="Nomor ID dari jawaban_form_2 yang akan diperbarui",
	 *         required=true,
	 *         @OA\Schema(type="integer", example=12)
	 *     ),
	 *     @OA\RequestBody(
	 *         required=true,
	 *         @OA\JsonContent(
	 *             required={"k_asesor", "bk_asesor"},
	 *             @OA\Property(property="k_asesor", type="string", example="Penilaian asesor"),
	 *             @OA\Property(property="bk_asesor", type="string", example="Bukti kompetensi dari asesor")
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Berhasil memperbarui data k_asesor dan bk_asesor",
	 *         @OA\JsonContent(
	 *             @OA\Property(property="status", type="integer", example=200),
	 *             @OA\Property(property="message", type="string", example="Data k_asesor dan bk_asesor berhasil diperbarui.")
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=404,
	 *         description="Data tidak ditemukan",
	 *         @OA\JsonContent(
	 *             @OA\Property(property="status", type="integer", example=404),
	 *             @OA\Property(property="message", type="string", example="Data jawaban_form_2 dengan no_id tersebut tidak ditemukan.")
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=422,
	 *         description="Validasi gagal",
	 *         @OA\JsonContent(
	 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
	 *             @OA\Property(property="errors", type="object")
	 *         )
	 *     )
	 * )
	 */
	 
	public function updateJawabanForm2ByNoId(Request $request)
	{
		// Autentikasi user (misal pakai JWTAuth, bisa disesuaikan dengan sistem Anda)
		$user = auth()->user(); // Jika pakai Laravel Sanctum atau default guard
		// $user = JWTAuth::parseToken()->authenticate(); // Jika pakai JWT

		// Cek role_id
		if (!$user || $user->role_id != 2) {
			return response()->json([
				'status' => 403,
				'message' => 'Akses ditolak. Hanya pengguna dengan role_id 2 yang dapat mengupdate data.'
			], 403);
		}

		// Validasi input array
		$this->validate($request, [
			'data' => 'required|array',
			'data.*.no_id' => 'required|integer',
			'data.*.k_asesor' => 'required|boolean',
			'data.*.bk_asesor' => 'required|boolean',
		]);

		$updated = 0;
		$notFound = [];

		foreach ($request->data as $item) {
			$no_id = $item['no_id'];

			// Cek apakah no_id ada
			$exists = DB::table('jawaban_form_2')->where('no_id', $no_id)->exists();

			if (!$exists) {
				$notFound[] = $no_id;
				continue;
			}

			// Lakukan update
			DB::table('jawaban_form_2')
				->where('no_id', $no_id)
				->update([
					'k_asesor' => (int) $item['k_asesor'],
					'bk_asesor' => (int) $item['bk_asesor'],
				]);

			$updated++;
		}

		return response()->json([
			'status' => 200,
			'message' => 'Update selesai.',
			'updated_count' => $updated,
			'not_found' => $notFound,
		]);
	}


public function updateIfEmptyByUserJawabForm2Id($user_jawab_form2_id)
{
    // Ambil semua baris dengan user_jawab_form2_id yang sama
    $records = DB::table('jawaban_form_2')
        ->where('user_jawab_form_2_id', $user_jawab_form2_id)
        ->get();

    // Jika tidak ada data ditemukan
    if ($records->isEmpty()) {
        return response()->json([
            'status' => 404,
            'message' => 'Tidak ada data ditemukan untuk user_jawab_form2_id tersebut.'
        ]);
    }

    $updatedRows = 0;

    foreach ($records as $record) {
        $updateData = [];

        if (is_null($record->k_asesor) || $record->k_asesor === '') {
            $updateData['k_asesor'] = $record->k;
        }

        if (is_null($record->bk_asesor) || $record->bk_asesor === '') {
            $updateData['bk_asesor'] = $record->bk;
        }

        if (!empty($updateData)) {
            DB::table('jawaban_form_2')
                ->where('no_id', $record->no_id)
                ->update($updateData);

            $updatedRows++;
        }
    }

    return response()->json([
        'status' => 200,
        'message' => $updatedRows > 0
            ? "$updatedRows baris berhasil diperbarui."
            : "Semua data sudah lengkap, tidak ada yang diperbarui.",
        'updated_count' => $updatedRows
    ]);
}



}
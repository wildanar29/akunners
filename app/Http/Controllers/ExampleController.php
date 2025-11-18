<?php

namespace App\Http\Controllers;
use App\Service\FormService;
use Illuminate\Http\Request;
use App\Models\DaftarUser;
use Illuminate\Support\Facades\Log;

class ExampleController extends Controller
{

    protected $formService;
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct(FormService $formService)
    {
        $this->formService = $formService;
    }   

    public function testNotificationAkunners(Request $request)
    {
        $user = DaftarUser::find($request->user_id);

        if (!$user) {
            \Log::error('❌ Notifikasi GAGAL dikirim: User tidak ditemukan', [
                'user_id' => $request->user_id
            ]);
            return;
        }
        try {
            $this->formService->kirimNotifikasiKeUser($user, 'Testing Notifikasi AKUNers', 'ini adalah pesan notifikasi percobaan dari AKUNers.');
            Log::info("Notifikasi DIKIRIM tanpa error", ['user_id' => $user->user_id]);
        } catch (\Exception $e) {
            Log::error("Gagal mengirim notifikasi", [
                'user_id' => $user->user_id,
                'error'   => $e->getMessage()
            ]);
        }
    }


    //
}

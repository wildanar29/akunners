<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\IjazahModel;
use App\Models\StrModel;
use App\Models\SipModel;
use App\Models\SpkModel;
use Illuminate\Support\Facades\DB;

class DocumentApprovalController extends Controller
{
    public function updateAllDocuments(Request $request)
    {
        $results = [];
        DB::beginTransaction();

        try {
            if ($request->has('ijazah')) {
                $results['ijazah'] = $this->processDocument($request->input('ijazah'), IjazahModel::class, 'ijazah');
            }

            if ($request->has('str')) {
                $results['str'] = $this->processDocument($request->input('str'), StrModel::class, 'str');
            }

            if ($request->has('sip')) {
                $results['sip'] = $this->processDocument($request->input('sip'), SipModel::class, 'sip');
            }

            if ($request->has('spk')) {
                $results['spk'] = $this->processDocument($request->input('spk'), SpkModel::class, 'spk');
            }

            if (empty($results)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Tidak ada dokumen yang dikirim dalam request.',
                ], 400);
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Semua dokumen berhasil diperbarui.',
                'data'    => $results,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui dokumen.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    private function processDocument($docData, $modelClass, $type)
    {
        $validator = Validator::make($docData, [
            "{$type}_id" => "required|integer|exists:users_{$type}_file,{$type}_id",
            'valid'      => 'nullable|boolean',
            'authentic'  => 'nullable|boolean',
            'current'    => 'nullable|boolean',
            'sufficient' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return [
                'status'  => 'validation_error',
                'message' => $validator->errors(),
            ];
        }

        $id = $docData["{$type}_id"];
        $updateData = collect($docData)
            ->only(['valid', 'authentic', 'current', 'sufficient'])
            ->filter(fn($v) => !is_null($v))
            ->toArray();

        if (empty($updateData)) {
            return [
                'status'  => 'no_fields_provided',
                'id'      => $id,
                'message' => 'Tidak ada field status yang dikirim untuk diperbarui.',
            ];
        }

        $record = $modelClass::find($id);
        if (!$record) {
            return [
                'status'  => 'not_found',
                'id'      => $id,
                'message' => "Data {$type} tidak ditemukan.",
            ];
        }

        $record->update($updateData);

        return [
            'status'  => 'updated',
            'id'      => $id,
            'updated_fields' => $updateData,
            'data'    => $record,
        ];
    }
}

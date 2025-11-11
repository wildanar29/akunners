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

    public function panduanForm2()
    {
        // Buat konten HTML dengan gaya profesional dan penekanan aturan
        $html = '<!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Panduan Penilaian Mandiri - Form 3 D</title>
            <style>
                body {
                    font-family: "Segoe UI", Arial, sans-serif;
                    line-height: 1.7;
                    color: #333;
                    margin: 30px;
                    background-color: #fafafa;
                }
                h2 {
                    text-align: center;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                    color: #222;
                }
                h3 {
                    margin-top: 25px;
                    color: #444;
                }
                p.intro {
                    font-size: 15px;
                    background: #eef3f8;
                    border-left: 5px solid #007bff;
                    padding: 10px 15px;
                    border-radius: 4px;
                }
                ol {
                    margin-top: 15px;
                    margin-left: 25px;
                    font-size: 15px;
                }
                li {
                    margin-bottom: 10px;
                }
                strong {
                    color: #d9534f;
                }
                hr {
                    border: none;
                    border-top: 2px solid #007bff;
                    margin: 20px 0;
                }
                footer {
                    margin-top: 30px;
                    font-size: 13px;
                    text-align: center;
                    color: #666;
                    font-style: italic;
                }
            </style>
        </head>
        <body>
            <h2>Panduan Penilaian Mandiri</h2>
            <hr>
            <p class="intro">
                <strong>Perhatian:</strong> Panduan ini merupakan <u>aturan resmi</u> yang <strong>wajib diikuti oleh seluruh peserta asesmen</strong>.
                Setiap peserta diharapkan membaca dengan seksama dan melaksanakan seluruh petunjuk berikut sesuai dengan ketentuan yang berlaku.
            </p>

            <h3>Petunjuk:</h3>
            <ol>
                <li>Pelajari seluruh standar <strong>Kriteria Unjuk Kerja (KUK)</strong> pada Standar Kompetensi dan pahami dengan seksama.</li>
                <li>Laksanakan penilaian mandiri secara jujur dan obyektif. Nilai kemampuan anda terhadap setiap pertanyaan, 
                    kemudian tentukan apakah sudah <strong>Kompeten (K)</strong> atau <strong>Belum Kompeten (BK)</strong>.</li>
                <li>Asesor akan menggunakan format ini pada saat <strong>konsultasi pra-asesmen</strong> untuk memvalidasi kesiapan anda, 
                    termasuk memberikan pertanyaan kritikal yang relevan dengan unit kompetensi.</li>
                <li>Setelah penilaian selesai, <strong>Asesor dan Asesi wajib menandatangani</strong> format asesmen mandiri sebagai bentuk pernyataan keabsahan data.</li>
            </ol>

            <footer>
                Dokumen ini menjadi bagian dari proses asesmen resmi dan tidak boleh diabaikan atau diubah tanpa persetujuan asesor.
            </footer>
        </body>
        </html>';

        // Return dalam format JSON seperti fungsi sebelumnya
        return response()->json([
            'status' => 'OK',
            'message' => 'Panduan penilaian mandiri berhasil ditampilkan.',
            'data' => $html
        ]);
    }

}

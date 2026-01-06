<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ReportUploadRequest;
use App\Http\Requests\DateRangeRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ReportsImport; 
use App\Models\Report;
use App\Models\Airport;
use App\Jobs\ProcessReportUpload;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ReportUploadController extends Controller
{
    /**
     * Upload dengan Queue untuk avoid timeout
     */
    public function upload(ReportUploadRequest $request) 
    {
        // 1. File sudah tervalidasi via ReportUploadRequest

        try {
            // 2. Baca Excel ke Array
            $arrays = Excel::toArray(new ReportsImport, $request->file('file'));
            $sheet = $arrays[0] ?? [];

            // 3. Generate unique upload ID untuk tracking
            $uploadId = Str::uuid()->toString();

            // 4. Audit Log - Upload Started
            Log::info('Report upload started', [
                'upload_id' => $uploadId,
                'filename' => $request->file('file')->getClientOriginalName(),
                'filesize' => $request->file('file')->getSize(),
                'total_rows' => count($sheet),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // 5. Dispatch ke Queue (Background Processing)
            ProcessReportUpload::dispatch($sheet, $uploadId);

            return response()->json([
                'message' => 'Upload dimulai. Proses berjalan di background.',
                'upload_id' => $uploadId,
                'total_rows' => count($sheet),
                'check_url' => "/api/upload-status/{$uploadId}"
            ], 202); // 202 = Accepted

        } catch (\Exception $e) {
            Log::error("Upload Error", [
                'error' => $e->getMessage(),
                'filename' => $request->file('file')->getClientOriginalName(),
                'ip_address' => $request->ip()
            ]);
            
            return response()->json([
                'error' => 'Gagal memproses file. ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check Upload Progress
     */
    public function uploadStatus($uploadId)
    {
        // Cek progress
        $progress = Cache::get("upload_progress_{$uploadId}");
        
        // Cek hasil akhir
        $result = Cache::get("upload_result_{$uploadId}");

        if ($result) {
            return response()->json($result);
        }

        if ($progress) {
            return response()->json([
                'status' => 'processing',
                'progress' => $progress
            ]);
        }

        return response()->json([
            'status' => 'not_found',
            'message' => 'Upload ID tidak ditemukan atau sudah expired'
        ], 404);
    }

    // --- HELPER FUNCTIONS ---

    /**
     * Logika Cerdas Mencari Lokasi
     */
    private function findAirportSmart($excelName)
    {
        $keyword = trim($excelName);
        if (empty($keyword)) return null;

        // 1. Cek Nama Kota (Paling Akurat untuk Cabang Utama)
        // Misal Excel="MEDAN", DB City="Medan" -> Ketemu Kualanamu
        $byCity = Airport::where('city', 'LIKE', $keyword)->first();
        if ($byCity) return $byCity;

        // 2. Cek Nama Bandara (Mengandung Kata)
        // Misal Excel="TANJUNG PINANG", DB Name="Cabang Tanjung Pinang"
        $byNameLike = Airport::where('name', 'LIKE', "%{$keyword}%")->first();
        if ($byNameLike) return $byNameLike;

        // 3. Cek ID Langsung (Jaga-jaga isinya kode)
        $byId = Airport::find($keyword);
        if ($byId) return $byId;

        return null;
    }

    /**
     * Konversi Tanggal Excel ke MySQL
     */
    private function transformExcelDate($value)
    {
        try {
            // Jika format angka serial Excel (misal: 45962)
            if (is_numeric($value)) {
                $unixDate = ($value - 25569) * 86400;
                return gmdate("Y-m-d H:i:s", $unixDate);
            }
            // Jika format teks biasa
            return Carbon::parse($value)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return now(); 
        }
    }

    private function mapStatus($raw)
    {
        $raw = strtolower($raw);
        if (str_contains($raw, 'completed') || str_contains($raw, 'closed')) return 'Analysis Completed';
        if (str_contains($raw, 'analyst')) return 'Analysis On Process';
        if (str_contains($raw, 'investigator')) return 'Send to Analyst';
        return 'Analysis On Process';
    }

    public function deleteRange(DateRangeRequest $request)
    {
        $validated = $request->validated();

        try {
            $cleanStartDate = Carbon::parse($validated['start_date'])->format('Y-m-d');
            $cleanEndDate   = Carbon::parse($validated['end_date'])->format('Y-m-d');
            
            // Soft delete (data masih bisa di-restore)
            $deletedCount = Report::whereDate('report_date', '>=', $cleanStartDate)
                                  ->whereDate('report_date', '<=', $cleanEndDate)
                                  ->delete();

            $startStr = Carbon::parse($cleanStartDate)->format('d M Y');
            $endStr   = Carbon::parse($cleanEndDate)->format('d M Y');

            // Audit Log
            Log::info('Reports deleted (soft)', [
                'count' => $deletedCount,
                'date_range' => [$cleanStartDate, $cleanEndDate],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Clear cache karena data berubah
            Cache::forget('airports_index');
            Cache::tags('airport_hierarchy')->flush();

            return response()->json([
                'message' => "Anda berhasil hapus {$deletedCount} data dari tanggal {$startStr} s/d {$endStr}.",
                'count' => $deletedCount,
                'note' => 'Data dapat di-restore dalam 30 hari'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Delete range failed', [
                'error' => $e->getMessage(),
                'date_range' => $validated,
                'ip_address' => $request->ip()
            ]);
            
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Restore deleted reports
     */
    public function restoreRange(DateRangeRequest $request)
    {
        $validated = $request->validated();

        try {
            $cleanStartDate = Carbon::parse($validated['start_date'])->format('Y-m-d');
            $cleanEndDate   = Carbon::parse($validated['end_date'])->format('Y-m-d');
            
            // Restore soft deleted records
            $restoredCount = Report::onlyTrashed()
                                  ->whereDate('report_date', '>=', $cleanStartDate)
                                  ->whereDate('report_date', '<=', $cleanEndDate)
                                  ->restore();

            $startStr = Carbon::parse($cleanStartDate)->format('d M Y');
            $endStr   = Carbon::parse($cleanEndDate)->format('d M Y');

            // Audit Log
            Log::info('Reports restored', [
                'count' => $restoredCount,
                'date_range' => [$cleanStartDate, $cleanEndDate],
                'ip_address' => $request->ip(),
            ]);

            // Clear cache
            Cache::forget('airports_index');
            Cache::tags('airport_hierarchy')->flush();

            return response()->json([
                'message' => "Berhasil restore {$restoredCount} data dari tanggal {$startStr} s/d {$endStr}.",
                'count' => $restoredCount
            ], 200);

        } catch (\Exception $e) {
            Log::error('Restore range failed', [
                'error' => $e->getMessage(),
                'date_range' => $validated,
            ]);
            
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
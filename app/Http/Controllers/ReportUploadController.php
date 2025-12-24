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
<<<<<<< HEAD
        // 1. File sudah tervalidasi via ReportUploadRequest

        try {
            // 2. Baca Excel ke Array
=======
        // 1. Validasi File
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls|max:20480' // Naikkan ke 20MB
        ]);

        // 2. Tingkatkan limit untuk file besar
        ini_set('max_execution_time', 300); // 5 menit
        ini_set('memory_limit', '512M');     // 512 MB

        try {
            // 3. Baca Excel ke Array
>>>>>>> 754487c (24/12)
            $arrays = Excel::toArray(new ReportsImport, $request->file('file'));
            $sheet = $arrays[0] ?? [];

<<<<<<< HEAD
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

=======
            $count = 0;
            $skipped = 0;
            $errors = [];
            $batchSize = 500; // Naikkan ke 500 untuk lebih cepat
            $currentBatch = [];

            // 4. LOAD SEMUA DATA EXISTING KE MEMORY (untuk pengecekan duplikat cepat)
            Log::info('Loading existing reports to memory...');
            $existingReports = Report::select('airport_id', 'report_date', 'description')
                ->get()
                ->map(function($report) {
                    // Buat unique key untuk setiap report
                    return $report->airport_id . '|' . $report->report_date . '|' . md5($report->description);
                })
                ->flip() // Ubah jadi key => value untuk lookup O(1)
                ->toArray();
            
            Log::info('Loaded ' . count($existingReports) . ' existing reports');

            DB::beginTransaction();
            
            // Disable foreign key checks untuk insert lebih cepat
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // 4. Loop Data dengan Batch Processing (Mulai dari baris ke-5, index 4)
            foreach ($sheet as $index => $row) {
                // Skip Header (Baris 1-4 di Excel = Index 0-3 di Array)
                if ($index < 4) continue; 

                // Ambil Data Kolom dari Excel (Sesuaikan Index dengan struktur file)
                // Index dimulai dari 0, jadi Kolom A = 0, B = 1, C = 2, dst.
                $effortId      = $row[1] ?? null;   // Kolom B - Id Effort
                $rawDate       = $row[2] ?? null;   // Kolom C - Date
                $inputDate     = $row[3] ?? null;   // Kolom D - Input Date
                $rawBranch     = $row[4] ?? null;   // Kolom E - Branch
                $location      = $row[5] ?? null;   // Kolom F - Location
                $atsUnit       = $row[6] ?? null;   // Kolom G - ATS Unit (TWR/APP/ACC)
                $classification = $row[7] ?? null;  // Kolom H - Classification
                $category      = $row[8] ?? 'Uncategorized';  // Kolom I - Category
                $ssrCode       = $row[9] ?? null;   // Kolom J - SSR Code
                
                // Kolom detail penerbangan (dari Excel)
                $aircraftId    = $row[10] ?? null;  // Kolom K - Aircraft Id (Call sign)
                $aircraftReg   = $row[11] ?? null;  // Kolom L - Aircraft Reg
                $aircraftType  = $row[12] ?? null;  // Kolom M - Type (B738/A320)
                $picName       = $row[13] ?? null;  // Kolom N - PIC
                $operator      = $row[14] ?? null;  // Kolom O - Operator
                $flightRules   = $row[15] ?? null;  // Kolom P - Frules (IFR/VFR)
                $flightPhase   = $row[16] ?? null;  // Kolom Q - Fphase (Phase of Flight)
                $departureAirport = $row[17] ?? null;  // Kolom R - ADEP
                $destinationAirport = $row[18] ?? null;  // Kolom S - ADES
                $typeF         = $row[19] ?? null;  // Kolom T - Typef
                $dta           = $row[20] ?? null;  // Kolom U - DTA
                $itp           = $row[21] ?? null;  // Kolom V - ITP
                $addInfo       = $row[22] ?? null;  // Kolom W - Add Info
                
                // Weather & Location
                $weatherCondition = $row[23] ?? null;  // Kolom X - Weather
                $flight        = $row[24] ?? null;  // Kolom Y - Flight
                $latitude      = $row[25] ?? null;  // Kolom Z - Lat
                $longitude     = $row[26] ?? null;  // Kolom AA - Long
                $altitude      = $row[27] ?? null;  // Kolom AB - Alt
                $horizontalDist = $row[28] ?? null; // Kolom AC - Horizontal Dist
                $verticalDist  = $row[29] ?? null;  // Kolom AD - Vertical Dist
                $timeQam       = $row[30] ?? null;  // Kolom AE - Time QAM
                
                // Weather detail
                $wind          = $row[31] ?? null;  // Kolom AF - Wind
                $visibility    = $row[32] ?? null;  // Kolom AG - Vis
                $pressureWx    = $row[33] ?? null;  // Kolom AH - Pres WX
                $cloud         = $row[34] ?? null;  // Kolom AI - Cloud
                $temperature   = $row[35] ?? null;  // Kolom AJ - Temp
                $altimeter     = $row[36] ?? null;  // Kolom AK - Altimeter
                
                // Description & Status
                $remark        = $row[37] ?? null;  // Kolom AL - Remark
                $desc          = $row[38] ?? '-';   // Kolom AM - Des (Description)
                $statusInvestigasi = $row[39] ?? null;  // Kolom AN - Status Investigasi
                $statusAnalyst = $row[40] ?? 'Open';    // Kolom AO - Status Analyst
                

                // Skip jika data vital kosong
                if (empty($rawBranch) || empty($rawDate)) {
                    continue;
                }

                // 5. SMART MATCHING LOKASI
                // Cari ID Airport berdasarkan nama di Excel
                $airport = $this->findAirportSmart($rawBranch);

                if (!$airport) {
                    $errors[] = "Baris " . ($index + 1) . ": Lokasi '$rawBranch' tidak ditemukan di database.";
                    continue; 
                }

                // 6. FORMAT TANGGAL
                $reportDate = $this->transformExcelDate($rawDate);
                $inputDateFormatted = $inputDate ? $this->transformExcelDate($inputDate) : null;

                // 6.5. CLEAN NUMERIC FIELDS - Convert '-', empty string, atau non-numeric ke null
                $cleanLat = $this->cleanNumericValue($latitude);
                $cleanLong = $this->cleanNumericValue($longitude);

                // 7. CEK DUPLIKASI - Gunakan memory lookup (sangat cepat!)
                $uniqueKey = $airport->id . '|' . $reportDate . '|' . md5($desc);
                
                if (isset($existingReports[$uniqueKey])) {
                    $skipped++;
                    continue; // Data sudah ada, lewati
                }
                
                // Tambahkan ke existing reports agar tidak duplikat dalam batch yang sama
                $existingReports[$uniqueKey] = true;

                // 8. TAMBAHKAN KE BATCH - SEMUA KOLOM LENGKAP
                $currentBatch[] = [
                    'airport_id'  => $airport->id,
                    'effort_id'   => $effortId,
                    'report_date' => $reportDate,
                    'input_date'  => $inputDateFormatted,
                    'category'    => $category,
                    'classification' => $classification,
                    'ssr_code'    => $ssrCode,
                    'description' => $desc,
                    'status'      => $this->mapStatus($statusAnalyst),
                    'location'    => $location,
                    'ats_unit'    => $atsUnit,
                    // Detail aircraft & flight
                    'aircraft_id' => $aircraftId,
                    'aircraft_reg' => $aircraftReg,
                    'aircraft_type' => $aircraftType,
                    'pic_name' => $picName,
                    'operator' => $operator,
                    'flight_rules' => $flightRules,
                    'flight_phase' => $flightPhase,
                    'departure_airport' => $departureAirport,
                    'destination_airport' => $destinationAirport,
                    'flight_type' => $typeF,
                    'type_f' => $typeF,
                    'dta' => $dta,
                    'itp' => $itp,
                    'add_info' => $addInfo,
                    'flight' => $flight,
                    // Koordinat & Posisi (cleaned)
                    'latitude' => $cleanLat,
                    'longitude' => $cleanLong,
                    'altitude' => $altitude,
                    'horizontal_distance' => $horizontalDist,
                    'vertical_distance' => $verticalDist,
                    'time_qam' => $timeQam,
                    // Weather
                    'weather_condition' => $weatherCondition,
                    'wind' => $wind,
                    'visibility' => $visibility,
                    'pressure_wx' => $pressureWx,
                    'cloud' => $cloud,
                    'temperature' => $temperature,
                    'altimeter' => $altimeter,
                    'remark' => $remark,
                    'status_investigasi' => $statusInvestigasi,
                    'status_analyst' => $statusAnalyst,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];

                // 9. SIMPAN BATCH JIKA SUDAH MENCAPAI LIMIT
                if (count($currentBatch) >= $batchSize) {
                    Report::insert($currentBatch);
                    $count += count($currentBatch);
                    Log::info("Inserted batch: {$count} total rows processed");
                    $currentBatch = []; // Reset batch
                }
            }

            // 10. SIMPAN SISA BATCH
            if (count($currentBatch) > 0) {
                Report::insert($currentBatch);
                $count += count($currentBatch);
            }

            // Enable kembali foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            
            DB::commit();
            Log::info("Upload completed: {$count} new records, {$skipped} skipped");

            $message = "Selesai. $count data baru berhasil diimpor.";
            if ($skipped > 0) {
                $message .= " ($skipped data dilewati karena sudah ada).";
            }
            
>>>>>>> 754487c (24/12)
            return response()->json([
                'message' => 'Upload dimulai. Proses berjalan di background.',
                'upload_id' => $uploadId,
                'total_rows' => count($sheet),
                'check_url' => "/api/upload-status/{$uploadId}"
            ], 202); // 202 = Accepted

        } catch (\Exception $e) {
<<<<<<< HEAD
            Log::error("Upload Error", [
                'error' => $e->getMessage(),
                'filename' => $request->file('file')->getClientOriginalName(),
                'ip_address' => $request->ip()
            ]);
=======
            DB::rollBack();
            
            // Pastikan foreign key checks di-enable kembali meskipun error
            try {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            } catch (\Exception $fkError) {
                // Ignore jika gagal
            }
            
            Log::error("Upload Error: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
>>>>>>> 754487c (24/12)
            
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

<<<<<<< HEAD
    public function deleteRange(DateRangeRequest $request)
=======
    /**
     * Clean numeric values - Convert '-', empty string, atau non-numeric ke null
     */
    private function cleanNumericValue($value)
    {
        // Jika null atau empty string, return null
        if ($value === null || $value === '' || $value === '-') {
            return null;
        }
        
        // Jika string, trim dulu
        if (is_string($value)) {
            $value = trim($value);
        }
        
        // Cek apakah numeric (angka atau string yang berisi angka)
        if (is_numeric($value)) {
            return $value;
        }
        
        // Jika bukan numeric, return null
        return null;
    }

    public function deleteRange(Request $request)
>>>>>>> 754487c (24/12)
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
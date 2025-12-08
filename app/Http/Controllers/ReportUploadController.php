<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ReportsImport; 
use App\Models\Report;
use App\Models\Airport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportUploadController extends Controller
{
    public function upload(Request $request) 
    {
        // 1. Validasi File
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls|max:10240'
        ]);

        DB::beginTransaction();

        try {
            // 2. Baca Excel ke Array
            // Kita gunakan class kosong ReportsImport hanya sebagai perantara
            $arrays = Excel::toArray(new ReportsImport, $request->file('file'));
            $sheet = $arrays[0] ?? []; // Ambil sheet pertama

            $count = 0;
            $skipped = 0;
            $errors = [];

            // 3. Loop Data (Mulai dari baris ke-5, index 4)
            foreach ($sheet as $index => $row) {
                // Skip Header (Baris 1-4 di Excel = Index 0-3 di Array)
                if ($index < 4) continue; 

                // Ambil Data Kolom (Sesuaikan Index Kolom dengan Excel Anda)
                // A=0, B=1, C=2 (Date), E=4 (Branch/Location), I=8 (Category), AL=37 (Desc), AN=39 (Status)
                
                $rawDate   = $row[2] ?? null; 
                $rawBranch = $row[4] ?? null;
                $category  = $row[8] ?? 'Uncategorized';
                $desc      = $row[37] ?? '-';
                
                // Ambil status (Cek kolom Analyst, jika kosong cek Investigasi)
                $statusRaw = $row[39] ?? $row[38] ?? 'Open'; 

                // Skip jika data vital kosong
                if (empty($rawBranch) || empty($rawDate)) {
                    continue;
                }

                // 4. SMART MATCHING LOKASI
                // Cari ID Airport berdasarkan nama di Excel
                $airport = $this->findAirportSmart($rawBranch);

                if (!$airport) {
                    $errors[] = "Baris " . ($index + 1) . ": Lokasi '$rawBranch' tidak ditemukan di database.";
                    continue; 
                }

                // 5. FORMAT TANGGAL
                $reportDate = $this->transformExcelDate($rawDate);

                // 6. CEK DUPLIKASI (Agar tidak double kalau diupload ulang)
                // Kriteria: Airport sama, Tanggal sama, Deskripsi sama
                $exists = Report::where('airport_id', $airport->id)
                                ->where('report_date', $reportDate)
                                ->where('description', $desc)
                                ->exists();

                if ($exists) {
                    $skipped++;
                    continue; // Lewati proses simpan, lanjut ke baris berikutnya
                }

                // 7. SIMPAN KE TABEL REPORTS
                Report::create([
                    'airport_id'  => $airport->id,
                    'report_date' => $reportDate,
                    'category'    => $category,
                    'description' => $desc,
                    'status'      => $this->mapStatus($statusRaw),
                    'location'    => $rawBranch, // Simpan nama asli excel sebagai referensi
                ]);

                $count++;
            }

            DB::commit();

            $message = "Selesai. $count data baru berhasil diimpor.";
            if ($skipped > 0) {
                $message .= " ($skipped data dilewati karena sudah ada).";
            }
            
            return response()->json([
                'message' => $message,
                'errors' => $errors 
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Upload Error: " . $e->getMessage());
            
            return response()->json([
                'error' => 'Gagal memproses data. ' . $e->getMessage()
            ], 500);
        }
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

    public function deleteRange(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date|before_or_equal:today',
            'end_date'   => 'required|date|after_or_equal:start_date|before_or_equal:today',
        ]);

        try {
            $cleanStartDate = Carbon::parse($validated['start_date'])->format('Y-m-d');
            $cleanEndDate   = Carbon::parse($validated['end_date'])->format('Y-m-d');
            
            $deletedCount = Report::whereDate('report_date', '>=', $cleanStartDate)
                                  ->whereDate('report_date', '<=', $cleanEndDate)
                                  ->delete();

            $startStr = Carbon::parse($cleanStartDate)->format('d M Y');
            $endStr   = Carbon::parse($cleanEndDate)->format('d M Y');

            return response()->json([
                'message' => "Anda berhasil hapus {$deletedCount} data dari tanggal {$startStr} s/d {$endStr}.",
                'count' => $deletedCount
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
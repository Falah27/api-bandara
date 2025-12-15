<?php

namespace App\Jobs;

use App\Models\Report;
use App\Models\Airport;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ProcessReportUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $rows;
    protected $uploadId;

    /**
     * Timeout 10 menit untuk upload besar
     */
    public $timeout = 600;

    /**
     * Retry 3x jika gagal
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(array $rows, string $uploadId)
    {
        $this->rows = $rows;
        $this->uploadId = $uploadId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $count = 0;
        $skipped = 0;
        $errors = [];
        $total = count($this->rows);

        foreach ($this->rows as $index => $row) {
            // Update progress setiap 50 rows
            if ($index % 50 === 0) {
                Cache::put("upload_progress_{$this->uploadId}", [
                    'processed' => $index,
                    'total' => $total,
                    'percentage' => round(($index / $total) * 100, 1)
                ], 3600);
            }

            try {
                // Skip Header (Baris 1-4 di Excel = Index 0-3 di Array)
                if ($index < 4) continue;

                $rawDate   = $row[2] ?? null;
                $rawBranch = $row[4] ?? null;
                $category  = $row[8] ?? 'Uncategorized';
                $desc      = $row[37] ?? '-';
                $statusRaw = $row[39] ?? $row[38] ?? 'Open';

                // Skip jika data vital kosong
                if (empty($rawBranch) || empty($rawDate)) {
                    continue;
                }

                // Smart Matching Lokasi
                $airport = $this->findAirportSmart($rawBranch);

                if (!$airport) {
                    $errors[] = "Baris " . ($index + 1) . ": Lokasi '$rawBranch' tidak ditemukan.";
                    continue;
                }

                // Format Tanggal
                $reportDate = $this->transformExcelDate($rawDate);

                // Cek Duplikasi
                $exists = Report::where('airport_id', $airport->id)
                    ->where('report_date', $reportDate)
                    ->where('description', $desc)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                // Simpan ke Database
                Report::create([
                    'airport_id'  => $airport->id,
                    'report_date' => $reportDate,
                    'category'    => $category,
                    'description' => $desc,
                    'status'      => $this->mapStatus($statusRaw),
                    'location'    => $rawBranch,
                ]);

                $count++;

            } catch (\Exception $e) {
                $errors[] = "Baris " . ($index + 1) . ": " . $e->getMessage();
                Log::error("Row {$index} error: " . $e->getMessage());
            }
        }

        // Simpan hasil akhir
        Cache::put("upload_result_{$this->uploadId}", [
            'status' => 'completed',
            'success_count' => $count,
            'skipped_count' => $skipped,
            'error_count' => count($errors),
            'errors' => array_slice($errors, 0, 50), // Max 50 errors untuk tidak overload
            'completed_at' => now()->toDateTimeString()
        ], 3600);

        // Clear cache airports karena ada data baru
        Cache::forget('airports_index');
        Cache::tags('airport_hierarchy')->flush();
    }

    /**
     * Logika Cerdas Mencari Lokasi - Optimized dengan mapping table
     */
    private function findAirportSmart($excelName)
    {
        $keyword = trim($excelName);
        if (empty($keyword)) return null;

        // 1. Cek mapping table terlebih dahulu (fastest)
        $mapping = \App\Models\AirportNameMapping::where('excel_name', $keyword)->first();
        if ($mapping) {
            $mapping->incrementUsage(); // Track usage
            return $mapping->airport;
        }

        // 2. Jika belum ada mapping, cari dan buat mapping baru
        $airport = Cache::remember("airport_lookup_{$keyword}", 3600, function () use ($keyword) {
            // Cek Nama Kota
            $byCity = Airport::where('city', 'LIKE', $keyword)->first();
            if ($byCity) return ['airport' => $byCity, 'type' => 'city'];

            // Cek Nama Bandara (Mengandung Kata)
            $byNameLike = Airport::where('name', 'LIKE', "%{$keyword}%")->first();
            if ($byNameLike) return ['airport' => $byNameLike, 'type' => 'name_like'];

            // Cek ID Langsung
            $byId = Airport::find($keyword);
            if ($byId) return ['airport' => $byId, 'type' => 'exact'];

            return null;
        });

        if ($airport) {
            // Simpan mapping untuk next time (avoid repeated lookups)
            \App\Models\AirportNameMapping::firstOrCreate([
                'excel_name' => $keyword,
                'airport_id' => $airport['airport']->id,
            ], [
                'match_type' => $airport['type'],
                'match_count' => 1
            ]);

            return $airport['airport'];
        }

        return null;
    }

    /**
     * Konversi Tanggal Excel ke MySQL
     */
    private function transformExcelDate($value)
    {
        try {
            if (is_numeric($value)) {
                $unixDate = ($value - 25569) * 86400;
                return gmdate("Y-m-d H:i:s", $unixDate);
            }
            return Carbon::parse($value)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return now();
        }
    }

    /**
     * Map Status
     */
    private function mapStatus($raw)
    {
        $raw = strtolower($raw);
        if (str_contains($raw, 'completed') || str_contains($raw, 'closed')) return 'Analysis Completed';
        if (str_contains($raw, 'analyst')) return 'Analysis On Process';
        if (str_contains($raw, 'investigator')) return 'Send to Analyst';
        return 'Analysis On Process';
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception)
    {
        Cache::put("upload_result_{$this->uploadId}", [
            'status' => 'failed',
            'error' => $exception->getMessage(),
            'failed_at' => now()->toDateTimeString()
        ], 3600);

        Log::error("Upload job {$this->uploadId} failed: " . $exception->getMessage());
    }
}

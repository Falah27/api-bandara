<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Airport;
use App\Models\Report;
use Carbon\Carbon;

class RawReportSeeder extends Seeder
{
    public function run()
    {
        $this->command->info("Memulai impor data laporan mentah (8000+ baris)...");
        
        // Kosongkan tabel reports
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Report::truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $path = database_path('seeders/data/csv-data-raw.csv');
        if (!file_exists($path)) {
            $this->command->error("File csv-data-raw.csv tidak ditemukan.");
            return;
        }

        $file = fopen($path, 'r');
        
        // Lewati 3 baris pertama
        for ($i = 0; $i < 3; $i++) { fgetcsv($file); }

        // Baca header
        $headerRowString = fgets($file); 
        $delimiter = (substr_count($headerRowString, ';') > substr_count($headerRowString, ',')) ? ';' : ',';
        $headers = str_getcsv($headerRowString, $delimiter);

        $branchIndex = array_search('Branch', $headers);
        $dateIndex = array_search('Date', $headers);
        $categoryIndex = array_search('Category', $headers);
        $statusIndex = array_search('Status Analyst', $headers);
        $descIndex = array_search('Des', $headers);

        // --- PERBAIKAN DI SINI: Normalisasi Nama Bandara ke UPPERCASE ---
        // Kita buat array map: "AMBON" => "AMQ", "JATSC" => "JATSC"
        $airportCache = Airport::all()->pluck('id', 'name')->mapWithKeys(function ($item, $key) {
            return [strtoupper($key) => $item];
        })->toArray();
        // ---------------------------------------------------------------
        
        $reportsToInsert = []; 
        $rowCount = 0;
        
        while (($row = fgetcsv($file, 0, $delimiter)) !== FALSE) {
            if (empty($row) || !isset($row[$branchIndex])) continue;
            
            // Ubah nama di CSV jadi HURUF BESAR juga biar cocok
            $branchName = strtoupper(trim($row[$branchIndex]));
            
            // Cek apakah ada di cache (yang sudah di-uppercase)
            if (!isset($airportCache[$branchName])) {
                // Uncomment baris bawah ini kalau mau lihat mana yang gagal (optional)
                // $this->command->warn("Gagal mencocokkan: $branchName"); 
                continue; 
            }

            $dateString = trim($row[$dateIndex]);
            $parsedDate = null;

            try {
                $parsedDate = Carbon::createFromFormat('d/m/Y H:i', $dateString);
            } catch (\Exception $e) {
                try {
                    $parsedDate = Carbon::createFromFormat('d/m/Y H:i:s', $dateString);
                } catch (\Exception $e2) {
                    try {
                        $parsedDate = Carbon::parse($dateString);
                    } catch (\Exception $e3) {
                        $parsedDate = null; 
                    }
                }
            }

            if ($parsedDate) {
                $reportsToInsert[] = [
                    'airport_id'  => $airportCache[$branchName],
                    'report_date' => $parsedDate,
                    'category'    => $row[$categoryIndex] ?? null,
                    'status'      => $row[$statusIndex] ?? null,
                    'description' => $row[$descIndex] ?? null,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
                $rowCount++;
            }
        }
        fclose($file);

        $this->command->info("Memasukkan {$rowCount} laporan ke database...");
        foreach (array_chunk($reportsToInsert, 500) as $chunk) {
            Report::insert($chunk);
        }
        $this->command->info("Selesai mengimpor data mentah.");
    }
}
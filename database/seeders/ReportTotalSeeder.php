<?php

namespace Database\Seeders;

use App\Models\Airport;
use Illuminate\Database\Seeder;

class ReportTotalSeeder extends Seeder
{
    public function run()
    {
        $this->command->info("Memperbarui total laporan dari CSV...");
        $path = database_path('seeders/data/report_data.csv');

        if (!file_exists($path)) {
            $this->command->error("File report_data.csv tidak ditemukan.");
            return;
        }

        $file = fopen($path, 'r');
        fgetcsv($file); // Lewati header 1
        
        $headerRowString = fgets($file); 
        $delimiter = (substr_count($headerRowString, ';') > substr_count($headerRowString, ',')) ? ';' : ',';
        $headers = str_getcsv($headerRowString, $delimiter);

        // Cari indeks kolom "Grand Total"
        $totalColIndex = -1;
        foreach ($headers as $index => $name) {
            if (trim($name) == "Grand Total") {
                $totalColIndex = $index;
                break;
            }
        }

        if ($totalColIndex == -1) {
            $this->command->error("Kolom 'Grand Total' tidak ditemukan di CSV.");
            fclose($file);
            return;
        }

        // Loop baris data
        while (($row = fgetcsv($file, 0, $delimiter)) !== FALSE) {
            if (empty($row) || !isset($row[0])) continue;

            $cabangName = trim($row[0]);
            if ($cabangName == "Grand Total" || $cabangName == "") continue;

            $airport = Airport::where('name', 'LIKE', $cabangName)->first();
            if ($airport) {
                // Update HANYA total_reports
                $airport->total_reports = (int) $row[$totalColIndex];
                $airport->save();
            }
        }

        fclose($file);
        $this->command->info("Selesai memperbarui total laporan.");
    }
}
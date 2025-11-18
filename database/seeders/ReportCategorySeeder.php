<?php

namespace Database\Seeders;

use App\Models\Airport;
use Illuminate\Database\Seeder;

class ReportCategorySeeder extends Seeder
{
    public function run()
    {
        $this->command->info("Memperbarui Rincian Kategori Laporan dari CSV...");
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

        // Cari indeks "Grand Total" HANYA untuk dilewati
        $totalColIndex = -1;
        foreach ($headers as $index => $name) {
            if (trim($name) == "Grand Total") {
                $totalColIndex = $index;
                break;
            }
        }

        while (($row = fgetcsv($file, 0, $delimiter)) !== FALSE) {
            if (empty($row) || !isset($row[0])) continue;

            $cabangName = trim($row[0]);
            if ($cabangName == "Grand Total" || $cabangName == "") continue;

            $airport = Airport::where('name', 'LIKE', $cabangName)->first();
            if ($airport) {
                // HANYA MENGURUS KATEGORI RINCIAN
                $categoriesJson = [];
                foreach ($headers as $index => $categoryName) {
                    // Lewati kolom "Row Labels", "Grand Total", dan yg kosong
                    if ($index == 0 || $index == $totalColIndex || $categoryName == "(blank)" || $categoryName == "") {
                        continue;
                    }

                    if (isset($row[$index])) {
                        $count = (int) $row[$index];
                        if ($count > 0) {
                            $categoriesJson[trim($categoryName)] = $count;
                        }
                    }
                }

                if (empty($categoriesJson)) {
                    $airport->report_categories = null;
                } else {
                    $airport->report_categories = $categoriesJson; 
                }
                
                $airport->save();
            }
        }

        fclose($file);
        $this->command->info("Selesai memperbarui rincian kategori.");
    }
}
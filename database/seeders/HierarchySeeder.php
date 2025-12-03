<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Airport;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class HierarchySeeder extends Seeder
{
    private $currentCabang = null;

    public function run()
    {
        $filePath = database_path('seeders/data/hierarchy_data.csv');
        
        if (!File::exists($filePath)) {
            $this->command->error("❌ File tidak ditemukan: {$filePath}");
            return;
        }

        // ✅ HAPUS DATA LAMA
        $this->command->info("🗑️  Menghapus data lama...");
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('airports')->whereIn('type', ['cabang', 'cabang_pembantu', 'unit'])->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $file = fopen($filePath, 'r');
        
        // Skip 2 baris header
        fgetcsv($file, 0, ';');
        fgetcsv($file, 0, ';');

        $importCount = 0;
        $batchData = [];

        while (($row = fgetcsv($file, 0, ';')) !== false) {
            // Skip row kosong
            if (empty($row[2])) {
                continue;
            }

            $jumlahCabang = trim($row[0] ?? '');
            $lokasi = trim($row[2] ?? '');

            if (empty($lokasi)) {
                continue;
            }

            // ===== DETEKSI TIPE =====
            $type = null;
            $parent = null;
            $id = null;

            // ✅ CABANG (ada angka di kolom A)
            if (!empty($jumlahCabang) && is_numeric($jumlahCabang)) {
                $type = 'cabang';
                $id = $this->generateCabangId($lokasi);
                $parent = null;
                $this->currentCabang = $id;
                
                $this->command->info("🔵 CABANG: {$id}");
            }
            // ✅ CABANG PEMBANTU (langsung ke cabang)
            else if (stripos($lokasi, 'Cabang Pembantu') !== false) {
                $type = 'cabang_pembantu';
                $id = $this->generateId($lokasi);
                $parent = $this->currentCabang; // ✅ Langsung ke cabang
                
                $this->command->info("🟢 CP: {$id} → {$parent}");
            }
            // ✅ UNIT atau KCP (langsung ke cabang)
            else if (stripos($lokasi, 'Unit') !== false || stripos($lokasi, 'KCP') !== false) {
                $type = 'unit';
                $id = $this->generateId($lokasi);
                $parent = $this->currentCabang; // ✅ Langsung ke cabang
                
                if ($importCount < 30) {
                    $this->command->info("🟡 UNIT: {$id} → {$parent}");
                }
            }
            else {
                continue;
            }

            // ===== EXTRACT INFO =====
            $city = $this->extractCity($lokasi);
            $provinsi = $this->guessProvinsi($city);
            $coordinates = $this->getCoordinates($city);

            // ✅ TAMBAHKAN KE BATCH
            $batchData[] = [
                'id' => $id,
                'type' => $type,
                'parent_id' => $parent,
                'name' => $lokasi,
                'city' => $city,
                'provinsi' => $provinsi,
                'coordinates' => json_encode($coordinates),
                'safetyReport' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now()
            ];

            $importCount++;

            // Insert per 100 rows
            if (count($batchData) >= 100) {
                DB::table('airports')->insert($batchData);
                $batchData = [];
            }
        }

        // Insert sisa data
        if (!empty($batchData)) {
            DB::table('airports')->insert($batchData);
        }

        fclose($file);
        
        $this->command->info("\n✅ Import selesai! Total: {$importCount} data");
        
        // ✅ VERIFICATION
        $this->verifyImport();
    }

    private function verifyImport()
    {
        $this->command->info("\n📊 VERIFICATION:");
        
        $cabangCount = DB::table('airports')->where('type', 'cabang')->count();
        $cpCount = DB::table('airports')->where('type', 'cabang_pembantu')->count();
        $unitCount = DB::table('airports')->where('type', 'unit')->count();
        $withParent = DB::table('airports')->whereNotNull('parent_id')->count();
        
        $this->command->table(
            ['Type', 'Count'],
            [
                ['Cabang', $cabangCount],
                ['Cabang Pembantu', $cpCount],
                ['Unit', $unitCount],
                ['With Parent', $withParent]
            ]
        );

        // ✅ TEST: Cek beberapa cabang
        $testCabang = ['MATSC', 'MEDAN', 'PALEMBANG'];
        
        foreach ($testCabang as $cabangId) {
            $children = DB::table('airports')
                ->where('parent_id', $cabangId)
                ->count();
            
            if ($children > 0) {
                $this->command->info("✅ {$cabangId} punya {$children} children");
                
                // Show sample
                $samples = DB::table('airports')
                    ->where('parent_id', $cabangId)
                    ->limit(3)
                    ->get(['id', 'name', 'type']);
                
                foreach ($samples as $sample) {
                    $this->command->line("   - {$sample->id}: {$sample->name} ({$sample->type})");
                }
            } else {
                $this->command->warn("⚠️  {$cabangId} tidak punya children");
            }
        }
    }

    private function generateCabangId($name)
    {
        $clean = preg_replace('/Cabang\s+/i', '', $name);
        $clean = strtoupper(trim($clean));
        return $clean;
    }

    private function generateId($name)
    {
        $clean = $name;
        
        if (stripos($name, 'Cabang Pembantu') !== false) {
            $clean = preg_replace('/Cabang Pembantu\s+/i', 'CP_', $name);
        } else if (stripos($name, 'Unit') !== false) {
            $clean = preg_replace('/Unit\s+/i', 'UNIT_', $name);
        } else if (stripos($name, 'KCP') !== false) {
            $clean = preg_replace('/KCP\s+/i', 'KCP_', $name);
        }
        
        $clean = strtoupper(trim($clean));
        $clean = str_replace([' ', ',', '.', '-', '(', ')'], '_', $clean);
        
        return $clean;
    }

    private function extractCity($name)
    {
        $clean = preg_replace('/^(Cabang|Cabang Pembantu|Unit|KCP)\s+/i', '', $name);
        $parts = explode(' ', trim($clean));
        return $parts[0];
    }

    private function getCoordinates($city)
    {
        $coords = [
            'JATSC' => [-6.2088, 106.8456],
            'MATSC' => [-5.0617, 119.5542],
            'MEDAN' => [3.5952, 98.6722],
            'PALEMBANG' => [-2.9761, 104.7754],
            'YOGYAKARTA' => [-7.7956, 110.3695],
            'SURABAYA' => [-7.2575, 112.7521],
            'DENPASAR' => [-8.6705, 115.2126],
            'BALIKPAPAN' => [-1.2675, 116.8289],
            'SENTANI' => [-2.5765, 140.5166],
            'PONTIANAK' => [-0.0263, 109.3425],
            'BANDA' => [5.5233, 95.4231],
            'PEKANBARU' => [0.5071, 101.4478],
            'TANJUNG' => [0.9223, 104.5369],
            'HALIM' => [-6.2661, 106.8907],
            'BANDUNG' => [-6.9034, 107.5755],
            'SEMARANG' => [-6.9734, 110.4070],
            'BANJARMASIN' => [-3.4422, 114.7631],
            'PALANGKARAYA' => [-2.2115, 113.9213],
            'TARAKAN' => [3.3268, 117.5696],
            'MANADO' => [1.5493, 124.9267],
            'KENDARI' => [-3.9782, 122.5892],
            'LOMBOK' => [-8.7568, 116.2725],
            'KUPANG' => [-10.1718, 123.6719],
            'AMBON' => [-3.7100, 128.0909],
            'WAMENA' => [-4.1030, 138.9570],
            'NABIRE' => [-3.3681, 135.4964],
            'SORONG' => [-0.8939, 131.2895],
            'MERAUKE' => [-8.5205, 140.4183],
            'PALU' => [-0.9183, 119.9094],
            'LUWUK' => [-0.9519, 122.7719],
            'MAMUJU' => [-2.6925, 118.8894],
        ];

        $cityUpper = strtoupper($city);
        
        if (isset($coords[$cityUpper])) {
            return $coords[$cityUpper];
        }

        foreach ($coords as $key => $value) {
            if (strpos($cityUpper, $key) !== false) {
                return $value;
            }
        }

        return [-2.5489, 118.0149];
    }

    private function guessProvinsi($city)
    {
        $mapping = [
            'JATSC' => 'DKI Jakarta',
            'MATSC' => 'Sulawesi Selatan',
            'MEDAN' => 'Sumatera Utara',
            'PALEMBANG' => 'Sumatera Selatan',
            'PALU' => 'Sulawesi Tengah',
        ];

        $cityUpper = strtoupper($city);
        return $mapping[$cityUpper] ?? 'Indonesia';
    }
}
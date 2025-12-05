<?php

namespace Database\Seeders;

use App\Models\Airport;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HierarchySeeder extends Seeder
{
    private $cabangUtamaMap = [
        'Cabang  JATSC' => 'JATSC', 'Cabang  MATSC' => 'MATSC',
        'Cabang  Medan' => 'KNO', 'Cabang  Palembang' => 'PLM',
        'Cabang  Yogyakarta' => 'YIA', 'Cabang  Surabaya' => 'SUB',
        'Cabang  Denpasar' => 'DPS', 'Cabang  Balikpapan' => 'BPN',
        'Cabang  Sentani' => 'DJJ', 'Cabang  Pontianak' => 'PNK',
        'Cabang  Banda Aceh' => 'BTJ', 'Cabang  Pekanbaru' => 'PKU',
        'Cabang  Tanjung Pinang' => 'TNJ', 'Cabang  Halim' => 'HLP',
        'Cabang  Bandung' => 'BDO', 'Cabang  Semarang' => 'SRG',
        'Cabang  Banjarmasin' => 'BDJ', 'Cabang  Palangkaraya' => 'PKY',
        'Cabang  Tarakan' => 'TRK', 'Cabang  Manado' => 'MDC',
        'Cabang  Kendari' => 'KDI', 'Cabang  Lombok' => 'LOP',
        'Cabang  Kupang' => 'KOE', 'Cabang  Ambon' => 'AMQ',
        'Cabang  Wamena' => 'WMX', 'Cabang  Nabire' => 'NBX',
        'Cabang  Sorong' => 'SOQ', 'Cabang  Merauke' => 'MKQ',
    ];

    private $provinsiMap = [];
    private $currentParent = null;
    private $stats = ['updated' => 0, 'inserted' => 0, 'skipped' => 0];

    public function run()
    {
        $this->command->info("🚀 Hierarchy Seeder - 301 Lokasi\n");
        $this->cacheProvinsi();
        $data = $this->getCSVData();
        
        DB::beginTransaction();
        try {
            foreach ($data as $i => $row) {
                $this->processRow($row, $i + 1);
                if (($i + 1) % 20 == 0) $this->command->info("📝 Row " . ($i + 1) . "/301");
            }
            DB::commit();
            $this->printSummary();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("❌ " . $e->getMessage());
            throw $e;
        }
    }

    private function processRow($row, $num)
    {
        $lok = trim($row['lokasi']);
        if (empty($lok)) { $this->stats['skipped']++; return; }
        
        // Deteksi: Cabang Utama (double space)
        if (strpos($lok, 'Cabang  ') === 0) {
            $this->processCabangUtama($lok, $num);
        }
        // Deteksi: Cabang Pembantu atau KCP
        elseif (strpos($lok, 'Cabang Pembantu') !== false || strpos($lok, 'KCP') !== false) {
            $this->processLevel2($lok, $num, 'cabang_pembantu');
        }
        // Deteksi: Unit
        elseif (strpos($lok, 'Unit') === 0) {
            $this->processLevel2($lok, $num, 'unit');
        }
        else {
            $this->command->warn("⚠️  Row $num: Unknown type: $lok");
            $this->stats['skipped']++;
        }
    }

    private function processCabangUtama($lok, $num)
    {
        $id = $this->cabangUtamaMap[$lok] ?? null;
        if (!$id) {
            $this->command->warn("⚠️  Row $num: Mapping not found: $lok");
            $this->stats['skipped']++;
            return;
        }
        
        $airport = Airport::find($id);
        if (!$airport) {
            $this->command->warn("⚠️  Row $num: ID $id not in DB");
            $this->stats['skipped']++;
            return;
        }
        
        $airport->update(['level' => 'cabang_utama', 'parent_id' => null, 'is_active' => true]);
        $this->currentParent = $id;
        $this->stats['updated']++;
        $this->command->info("✅ Row $num: $lok ($id)");
    }

    private function processLevel2($lok, $num, $level)
    {
        if (!$this->currentParent) {
            $this->command->warn("⚠️  Row $num: No parent for $lok");
            $this->stats['skipped']++;
            return;
        }
        
        $newId = $this->generateId($this->currentParent, $lok);
        $city = $this->extractCity($lok);
        
        // Handle duplikat
        if (Airport::find($newId)) {
            $c = 2;
            while (Airport::find($newId . $c)) $c++;
            $newId = $newId . $c;
        }
        
        Airport::create([
            'id' => $newId, 'name' => $lok, 'code' => null, 'city' => $city,
            'provinsi' => $this->provinsiMap[$this->currentParent] ?? null,
            'parent_id' => $this->currentParent, 'level' => $level,
            'service_level' => null, 'coordinates' => null, 'safetyReport' => null,
            'is_active' => true, 'has_reports' => false, 'total_reports' => 0,
        ]);
        
        $this->stats['inserted']++;
    }

    private function cacheProvinsi()
    {
        $aps = Airport::whereIn('id', array_values($this->cabangUtamaMap))->get();
        foreach ($aps as $a) $this->provinsiMap[$a->id] = $a->provinsi;
    }

    private function generateId($parent, $name)
    {
        $clean = str_replace(['Cabang Pembantu', 'Unit', 'KCP', ','], '', $name);
        $clean = trim($clean);
        $words = explode(' ', $clean);
        $code = strtoupper(substr($words[0], 0, 3));
        return "$parent-$code";
    }

    private function extractCity($name)
    {
        $city = str_replace(['Cabang Pembantu', 'Unit', 'KCP'], '', $name);
        $city = trim($city);
        $words = explode(' ', $city);
        return implode(' ', array_slice($words, 0, 2));
    }

    private function printSummary()
    {
        $total = $this->stats['updated'] + $this->stats['inserted'];
        $this->command->info("\n" . str_repeat("=", 50));
        $this->command->info("📊 SUMMARY");
        $this->command->info(str_repeat("=", 50));
        $this->command->info("✅ Updated: {$this->stats['updated']}");
        $this->command->info("✅ Inserted: {$this->stats['inserted']}");
        $this->command->info("⚠️  Skipped: {$this->stats['skipped']}");
        $this->command->info("\n🎯 Total: $total / 301");
        if ($total == 301) $this->command->info("🎉 PERFECT!\n");
        else $this->command->warn("⚠️  Expected 301, got $total\n");
    }

    // DATA 301 LOKASI
    private function getCSVData() {
        return [
            ['lokasi'=>'Cabang  JATSC'],['lokasi'=>'Cabang  MATSC'],['lokasi'=>'Cabang Pembantu Palu'],
            ['lokasi'=>'Cabang Pembantu Luwuk'],['lokasi'=>'Unit Mamuju'],['lokasi'=>'Unit Masamba'],
            ['lokasi'=>'Unit Poso'],['lokasi'=>'Unit Toli Toli'],['lokasi'=>'Unit Buol'],
            ['lokasi'=>'Unit Tana Toraja'],['lokasi'=>'Unit Mamasa'],['lokasi'=>'Unit Bone'],
            ['lokasi'=>'Unit Seko'],['lokasi'=>'Unit Rampi'],['lokasi'=>'Unit Bua'],
            ['lokasi'=>'Unit Selayar'],['lokasi'=>'Unit Tojo Una-Una'],['lokasi'=>'Unit Banggai Laut'],
            ['lokasi'=>'Cabang  Medan'],['lokasi'=>'Cabang Pembantu Gunung sitoli'],['lokasi'=>'Unit Siborong Borong'],
            ['lokasi'=>'Unit Aek Godang'],['lokasi'=>'Unit Lasondre'],['lokasi'=>'Unit Sibolga'],
            ['lokasi'=>'Unit Parapat'],['lokasi'=>'Unit Mandailing Natal'],['lokasi'=>'Cabang  Palembang'],
            ['lokasi'=>'Cabang Pembantu Pangkal Pinang'],['lokasi'=>'Unit Tanjung Pandan'],['lokasi'=>'Unit Bandar Lampung'],
            ['lokasi'=>'Unit Way Kanan'],['lokasi'=>'Cabang Pembantu Jambi'],['lokasi'=>'Unit Muko Muko'],
            ['lokasi'=>'Unit Kerinci'],['lokasi'=>'Unit Muara Bungo'],['lokasi'=>'Unit Lubuk Linggau'],
            ['lokasi'=>'Cabang Pembantu Bengkulu'],['lokasi'=>'Unit Enggano'],['lokasi'=>'Unit Pagar Alam'],
            ['lokasi'=>'Unit Pekon Serai, Krui Lampung'],['lokasi'=>'Cabang  Yogyakarta'],['lokasi'=>'Cabang Pembantu Solo'],
            ['lokasi'=>'Unit Cilacap'],['lokasi'=>'Cabang  Surabaya'],['lokasi'=>'Cabang Pembantu Banyuwangi'],
            ['lokasi'=>'Cabang Pembantu Malang'],['lokasi'=>'Cabang Pembantu Sumenep'],['lokasi'=>'Unit Jember'],
            ['lokasi'=>'Unit Bawean'],['lokasi'=>'Unit Blora'],['lokasi'=>'Unit Kediri'],
            ['lokasi'=>'Cabang  Denpasar'],['lokasi'=>'Cabang Pembantu Labuan Bajo'],['lokasi'=>'Unit Waingapu'],
            ['lokasi'=>'Unit Tambolaka'],['lokasi'=>'Unit Pagerungan'],['lokasi'=>'Unit Buleleng'],
            ['lokasi'=>'Cabang  Balikpapan'],['lokasi'=>'Cabang Pembantu Samarinda'],['lokasi'=>'Cabang Pembantu Berau'],
            ['lokasi'=>'Unit Datah Dawai'],['lokasi'=>'Unit Melak'],['lokasi'=>'Unit Kota Bangun'],
            ['lokasi'=>'Unit Muara Wahau'],['lokasi'=>'Unit Kutai Timur'],['lokasi'=>'Unit Derawan'],
            ['lokasi'=>'KCP IKN'],['lokasi'=>'Cabang  Sentani'],['lokasi'=>'Cabang Pembantu Biak'],
            ['lokasi'=>'Cabang Pembantu Oksibil'],['lokasi'=>'Cabang Pembantu Timika'],['lokasi'=>'Unit Kiwirok'],
            ['lokasi'=>'Unit Dabra'],['lokasi'=>'Unit Batom'],['lokasi'=>'Unit Senggeh'],
            ['lokasi'=>'Unit Waris (Towe Hitam), Keerom'],['lokasi'=>'Unit Serui'],['lokasi'=>'Unit Numfor'],
            ['lokasi'=>'Unit Kokonao'],['lokasi'=>'Unit Mararena, Sarmi'],['lokasi'=>'Unit Akimuga'],
            ['lokasi'=>'Unit Abmisibil'],['lokasi'=>'Unit Aboy, Peg. Bintang'],['lokasi'=>'Unit Alama, Peg. Bintang'],
            ['lokasi'=>'Unit Jila, Mimika'],['lokasi'=>'Unit Jita, Mimika'],['lokasi'=>'Unit Kapiraya'],
            ['lokasi'=>'Unit Luban'],['lokasi'=>'Unit Okbab'],['lokasi'=>'Unit Potowai, Mimika'],
            ['lokasi'=>'Unit Tsinga, Mimika'],['lokasi'=>'Unit Ubrub, Keerom'],['lokasi'=>'Unit Wangbe, Keerom'],
            ['lokasi'=>'Unit Yuruf, Keerom'],['lokasi'=>'Unit Molof, Keerom'],['lokasi'=>'Unit Lereh, Keerom'],
            ['lokasi'=>'Unit Teraplu'],['lokasi'=>'Unit Kasonaweja'],['lokasi'=>'Cabang  Pontianak'],
            ['lokasi'=>'Cabang Pembantu Ketapang'],['lokasi'=>'Unit Sintang'],['lokasi'=>'Unit Putussibau'],
            ['lokasi'=>'Unit Nanga Pinoh'],['lokasi'=>'Unit Sambas'],['lokasi'=>'Unit Harapan, Manis Mata'],
            ['lokasi'=>'Unit Semelagi'],['lokasi'=>'Unit Singkawang'],['lokasi'=>'Cabang  Banda Aceh'],
            ['lokasi'=>'Unit Meulaboh'],['lokasi'=>'Unit Sinabang'],['lokasi'=>'Unit Takengon'],
            ['lokasi'=>'Unit Tapak Tuan'],['lokasi'=>'Unit Sabang'],['lokasi'=>'Unit Singkil'],
            ['lokasi'=>'Unit Kutacane'],['lokasi'=>'Unit Blang Pidi'],['lokasi'=>'Unit Lhok Seumawe'],
            ['lokasi'=>'Unit Gayo Lues'],['lokasi'=>'Cabang  Pekanbaru'],['lokasi'=>'Cabang Pembantu Rengat'],
            ['lokasi'=>'Unit Pasir Pangaraian'],['lokasi'=>'Unit Indragiri Hilir'],['lokasi'=>'Unit Dumai'],
            ['lokasi'=>'Unit Pelalawan'],['lokasi'=>'Cabang Pembantu Padang'],['lokasi'=>'Unit Rokot Sipora'],
            ['lokasi'=>'Unit Pasaman Barat'],['lokasi'=>'Cabang  Tanjung Pinang'],['lokasi'=>'Cabang Pembantu Batam'],
            ['lokasi'=>'Unit Singkep'],['lokasi'=>'Unit Tanjung Balai Karimun'],['lokasi'=>'Unit Bintan'],
            ['lokasi'=>'Cabang Pembantu Natuna'],['lokasi'=>'Unit Anambas (Tanjung Pinang)'],['lokasi'=>'Unit Matak'],
            ['lokasi'=>'Unit Tambelan'],['lokasi'=>'Cabang  Halim'],['lokasi'=>'Cabang Pembantu Curug'],
            ['lokasi'=>'Unit Pondok Cabe'],['lokasi'=>'Cabang  Bandung'],['lokasi'=>'Cabang Pembantu Cirebon'],
            ['lokasi'=>'Unit Pangandaran'],['lokasi'=>'Unit Tasikmalaya'],['lokasi'=>'Unit Kertajati'],
            ['lokasi'=>'Cabang  Semarang'],['lokasi'=>'Unit Karimun Jawa'],['lokasi'=>'Unit Purbalingga'],
            ['lokasi'=>'Cabang  Banjarmasin'],['lokasi'=>'Cabang Pembantu Pangkalan Bun'],['lokasi'=>'Cabang Pembantu Sampit'],
            ['lokasi'=>'Unit Kota Baru'],['lokasi'=>'Unit Kuala Pembuang'],['lokasi'=>'Unit Batu Licin'],
            ['lokasi'=>'Unit Tanjung Warukin'],['lokasi'=>'Cabang  Palangkaraya'],['lokasi'=>'Unit Muara Teweh'],
            ['lokasi'=>'Unit Kuala Kurun'],['lokasi'=>'Unit Buntok'],['lokasi'=>'Unit Tumbang Samba'],
            ['lokasi'=>'Unit Puruk Cahu'],['lokasi'=>'Cabang  Tarakan'],['lokasi'=>'Cabang Pembantu Malinau'],
            ['lokasi'=>'Unit Nunukan'],['lokasi'=>'Unit Long Bawan'],['lokasi'=>'Unit Long Ampung'],
            ['lokasi'=>'Unit Tanjung Harapan'],['lokasi'=>'Unit Long Layu'],['lokasi'=>'Unit Binuang'],
            ['lokasi'=>'Cabang  Manado'],['lokasi'=>'Cabang Pembantu Ternate'],['lokasi'=>'Cabang Pembantu Gorontalo'],
            ['lokasi'=>'Unit Labuha'],['lokasi'=>'Unit Morotai'],['lokasi'=>'Unit Melonguane'],
            ['lokasi'=>'Unit Kao'],['lokasi'=>'Unit Galela'],['lokasi'=>'Unit Buli Maba'],
            ['lokasi'=>'Unit Sanana'],['lokasi'=>'Unit Tahuna '],['lokasi'=>'Unit Halmahera Tengah'],
            ['lokasi'=>'Unit Manggole Kep. Sola'],['lokasi'=>'Unit Miangas'],['lokasi'=>'Unit Siau'],
            ['lokasi'=>'Unit Bolaang Mongondow'],['lokasi'=>'Unit Pohuwato'],['lokasi'=>'Unit Weda Tengah'],
            ['lokasi'=>'Cabang  Kendari'],['lokasi'=>'Unit Wakatobi'],['lokasi'=>'Unit Bau Bau'],
            ['lokasi'=>'Unit Kolaka'],['lokasi'=>'Unit Sugimanuru'],['lokasi'=>'Unit Morowali'],
            ['lokasi'=>'Unit Bahodopi'],['lokasi'=>'Cabang  Lombok'],['lokasi'=>'Cabang Pembantu Bima'],
            ['lokasi'=>'Cabang Pembantu Sumbawa'],['lokasi'=>'Unit Lunyuk, Sumbawa'],['lokasi'=>'Unit Poto Tano'],
            ['lokasi'=>'Cabang  Kupang'],['lokasi'=>'Cabang Pembantu Ende'],['lokasi'=>'Unit Maumere'],
            ['lokasi'=>'Unit Larantuka'],['lokasi'=>'Unit Rote'],['lokasi'=>'Unit Sabu'],
            ['lokasi'=>'Unit Bajawa'],['lokasi'=>'Unit Lewoleba'],['lokasi'=>'Unit Atambua'],
            ['lokasi'=>'Unit Ruteng'],['lokasi'=>'Unit Alor'],['lokasi'=>'Unit Pantar'],
            ['lokasi'=>'Cabang  Ambon'],['lokasi'=>'Cabang Pembantu Tual, Karel Sadsuitubun'],['lokasi'=>'Unit Dobo'],
            ['lokasi'=>'Unit Saumlaki'],['lokasi'=>'Unit Bandanaira'],['lokasi'=>'Unit Namrole'],
            ['lokasi'=>'Unit Larat'],['lokasi'=>'Unit Wahai'],['lokasi'=>'Unit Amahai'],
            ['lokasi'=>'Unit Moa'],['lokasi'=>'Unit Kuffar'],['lokasi'=>'Unit Namlea'],
            ['lokasi'=>'Unit Kisar'],['lokasi'=>'Cabang  Wamena'],['lokasi'=>'Unit Tiom'],
            ['lokasi'=>'Unit Karubaga'],['lokasi'=>'Unit Bokondini'],['lokasi'=>'Unit Nop Goliat Dekai, Yahukimo'],
            ['lokasi'=>'Unit Elelim'],['lokasi'=>'Unit Anggruk'],['lokasi'=>'Unit Yalimo'],
            ['lokasi'=>'Unit Holuwun'],['lokasi'=>'Unit Mamberamo Tengah'],['lokasi'=>'Unit Mamit'],
            ['lokasi'=>'Unit Ninia'],['lokasi'=>'Unit Pasema'],['lokasi'=>'Unit Sobaham'],
            ['lokasi'=>'Unit Silimo'],['lokasi'=>'Unit Suru-Suru'],['lokasi'=>'Unit Tolikara'],
            ['lokasi'=>'Unit Mapnduma, Nduga'],['lokasi'=>'Unit Mugi, Nduga'],['lokasi'=>'Unit Paro, Nduga'],
            ['lokasi'=>'Unit Mamberamo Raya'],['lokasi'=>'Unit Kenyam, Nduga'],['lokasi'=>'Cabang  Nabire'],
            ['lokasi'=>'Unit Illaga'],['lokasi'=>'Unit Bilorai'],['lokasi'=>'Unit Mulia'],
            ['lokasi'=>'Unit Moanamani'],['lokasi'=>'Unit Enarotali'],['lokasi'=>'Unit Waghete, Paniai'],
            ['lokasi'=>'Unit Illu'],['lokasi'=>'Unit Sinak'],['lokasi'=>'Unit Aboyaga, Nabire'],
            ['lokasi'=>'Unit Duma'],['lokasi'=>'Unit Obano, Paniai'],['lokasi'=>'Unit Obano, Intan Jaya'],
            ['lokasi'=>'Unit Botawa'],['lokasi'=>'Unit Beoga, Intan Jaya'],['lokasi'=>'Unit Bilai, Intan Jaya'],
            ['lokasi'=>'Unit Puncak Jaya'],['lokasi'=>'Cabang  Sorong'],['lokasi'=>'Cabang Pembantu Manokwari'],
            ['lokasi'=>'Unit Babo'],['lokasi'=>'Unit Bintuni'],['lokasi'=>'Unit Fak Fak'],
            ['lokasi'=>'Unit Kaimana'],['lokasi'=>'Unit Anggi'],['lokasi'=>'Unit Ayawasi'],
            ['lokasi'=>'Unit Kambuaya'],['lokasi'=>'Unit Inanwatan'],['lokasi'=>'Unit Marinda, Waisai, Raja Ampat'],
            ['lokasi'=>'Unit Teminabuan'],['lokasi'=>'Unit Kebar'],['lokasi'=>'Unit Merdey, Teluk Bintuni'],
            ['lokasi'=>'Unit Kabare'],['lokasi'=>'Unit Wasior'],['lokasi'=>'Unit Ransiki'],
            ['lokasi'=>'Unit Werur, Tambrauw, Papua Barat'],['lokasi'=>'Unit Segun, Sorong'],['lokasi'=>'Cabang  Merauke'],
            ['lokasi'=>'Cabang Pembantu Tanah Merah'],['lokasi'=>'Unit Ewer'],['lokasi'=>'Unit Kepi'],
            ['lokasi'=>'Unit Bade'],['lokasi'=>'Unit Kimam'],['lokasi'=>'Unit Okaba'],
            ['lokasi'=>'Unit Mindiptanah'],['lokasi'=>'Unit Kamur'],['lokasi'=>'Unit Bomakia'],
            ['lokasi'=>'Unit Senggo'],['lokasi'=>'Unit Manggelum'],['lokasi'=>'Unit Yaniruma'],
            ['lokasi'=>'Unit Wanggemalo'],['lokasi'=>'Unit Iwur'],['lokasi'=>'Unit Aboge'],
            ['lokasi'=>'Unit Wanam'],['lokasi'=>'Unit Borome'],['lokasi'=>'Unit Kebo, Paniai'],
            ['lokasi'=>'Unit Kilmit'],
        ];
    }
}
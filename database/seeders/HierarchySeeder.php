<?php

namespace Database\Seeders;

use App\Models\Airport;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class HierarchySeeder extends Seeder
{
    // MAPPING ID CABANG UTAMA
    private $cabangUtamaMap = [
        'Cabang JATSC' => 'JATSC', 'Cabang MATSC' => 'MATSC',
        'Cabang Medan' => 'KNO', 'Cabang Palembang' => 'PLM',
        'Cabang Yogyakarta' => 'YIA', 'Cabang Surabaya' => 'SUB',
        'Cabang Denpasar' => 'DPS', 'Cabang Balikpapan' => 'BPN',
        'Cabang Sentani' => 'DJJ', 'Cabang Pontianak' => 'PNK',
        'Cabang Banda Aceh' => 'BTJ', 'Cabang Pekanbaru' => 'PKU',
        'Cabang Tanjung Pinang' => 'TNJ', 'Cabang Halim' => 'HLP',
        'Cabang Bandung' => 'BDO', 'Cabang Semarang' => 'SRG',
        'Cabang Banjarmasin' => 'BDJ', 'Cabang Palangkaraya' => 'PKY',
        'Cabang Tarakan' => 'TRK', 'Cabang Manado' => 'MDC',
        'Cabang Kendari' => 'KDI', 'Cabang Lombok' => 'LOP',
        'Cabang Kupang' => 'KOE', 'Cabang Ambon' => 'AMQ',
        'Cabang Wamena' => 'WMX', 'Cabang Nabire' => 'NBX',
        'Cabang Sorong' => 'SOQ', 'Cabang Merauke' => 'MKQ',
    ];

    // DATA DETAIL (Koordinat)
    private $airportDetails = [
        'JATSC' => ['city' => 'Tangerang', 'provinsi' => 'Banten', 'coords' => [-6.1255, 106.6558]],
        'MATSC' => ['city' => 'Makassar', 'provinsi' => 'Sulawesi Selatan', 'coords' => [-5.0616, 119.5540]],
        'KNO' => ['city' => 'Deli Serdang', 'provinsi' => 'Sumatera Utara', 'coords' => [3.6422, 98.8852]],
        'PLM' => ['city' => 'Palembang', 'provinsi' => 'Sumatera Selatan', 'coords' => [-2.8980, 104.7006]],
        'YIA' => ['city' => 'Kulon Progo', 'provinsi' => 'DIY', 'coords' => [-7.9075, 110.0544]],
        'SUB' => ['city' => 'Sidoarjo', 'provinsi' => 'Jawa Timur', 'coords' => [-7.3798, 112.7868]],
        'DPS' => ['city' => 'Denpasar', 'provinsi' => 'Bali', 'coords' => [-8.7481, 115.1671]],
        'BPN' => ['city' => 'Balikpapan', 'provinsi' => 'Kalimantan Timur', 'coords' => [-1.2682, 116.8944]],
        'DJJ' => ['city' => 'Jayapura', 'provinsi' => 'Papua', 'coords' => [-2.5768, 140.5156]],
        'PNK' => ['city' => 'Pontianak', 'provinsi' => 'Kalimantan Barat', 'coords' => [-0.1494, 109.4036]],
        'BTJ' => ['city' => 'Banda Aceh', 'provinsi' => 'Aceh', 'coords' => [5.5244, 95.4170]],
        'PKU' => ['city' => 'Pekanbaru', 'provinsi' => 'Riau', 'coords' => [0.4605, 101.4443]],
        'TNJ' => ['city' => 'Tanjung Pinang', 'provinsi' => 'Kepulauan Riau', 'coords' => [0.9238, 104.5293]],
        'HLP' => ['city' => 'Jakarta Timur', 'provinsi' => 'DKI Jakarta', 'coords' => [-6.2657, 106.8906]],
        'BDO' => ['city' => 'Bandung', 'provinsi' => 'Jawa Barat', 'coords' => [-6.9006, 107.5763]],
        'SRG' => ['city' => 'Semarang', 'provinsi' => 'Jawa Tengah', 'coords' => [-6.9730, 110.3755]],
        'BDJ' => ['city' => 'Banjarbaru', 'provinsi' => 'Kalimantan Selatan', 'coords' => [-3.4474, 114.7617]],
        'PKY' => ['city' => 'Palangkaraya', 'provinsi' => 'Kalimantan Tengah', 'coords' => [-2.2246, 113.9439]],
        'TRK' => ['city' => 'Tarakan', 'provinsi' => 'Kalimantan Utara', 'coords' => [3.3263, 117.5652]],
        'MDC' => ['city' => 'Manado', 'provinsi' => 'Sulawesi Utara', 'coords' => [1.5342, 124.9252]],
        'KDI' => ['city' => 'Kendari', 'provinsi' => 'Sulawesi Tenggara', 'coords' => [-4.0833, 122.4166]],
        'LOP' => ['city' => 'Lombok Tengah', 'provinsi' => 'Nusa Tenggara Barat', 'coords' => [-8.7602, 116.2730]],
        'KOE' => ['city' => 'Kupang', 'provinsi' => 'Nusa Tenggara Timur', 'coords' => [-10.1764, 123.6669]],
        'AMQ' => ['city' => 'Ambon', 'provinsi' => 'Maluku', 'coords' => [-3.7075, 128.0894]],
        'WMX' => ['city' => 'Wamena', 'provinsi' => 'Papua Pegunungan', 'coords' => [-4.1008, 138.9567]],
        'NBX' => ['city' => 'Nabire', 'provinsi' => 'Papua Tengah', 'coords' => [-3.3592, 135.4856]],
        'SOQ' => ['city' => 'Sorong', 'provinsi' => 'Papua Barat Daya', 'coords' => [-0.9255, 131.2882]],
        'MKQ' => ['city' => 'Merauke', 'provinsi' => 'Papua Selatan', 'coords' => [-8.5203, 140.4192]],
    ];

    public function run()
    {
        // 1. DATA RAW LENGKAP & TERURUT (DIJAMIN 301 ITEM)
        $rawData = [
            // 1. JATSC
            ['lokasi'=>'Cabang  JATSC'],
            
            // 2. MATSC (16 Anak)
            ['lokasi'=>'Cabang  MATSC'],
            ['lokasi'=>'Cabang Pembantu Palu'],
            ['lokasi'=>'Cabang Pembantu Luwuk'],
            ['lokasi'=>'Unit Mamuju'],
            ['lokasi'=>'Unit Masamba'],
            ['lokasi'=>'Unit Poso'],
            ['lokasi'=>'Unit Toli Toli'],
            ['lokasi'=>'Unit Buol'],
            ['lokasi'=>'Unit Tana Toraja'],
            ['lokasi'=>'Unit Mamasa'],
            ['lokasi'=>'Unit Bone'],
            ['lokasi'=>'Unit Seko'],
            ['lokasi'=>'Unit Rampi'],
            ['lokasi'=>'Unit Bua'],
            ['lokasi'=>'Unit Selayar'],
            ['lokasi'=>'Unit Tojo Una-Una'],
            ['lokasi'=>'Unit Banggai Laut'],

            // 3. MEDAN (8 Anak)
            ['lokasi'=>'Cabang  Medan'],
            ['lokasi'=>'Cabang Pembantu Gunung sitoli'],
            ['lokasi'=>'Unit Siborong Borong'],
            ['lokasi'=>'Unit Aek Godang'],
            ['lokasi'=>'Unit Lasondre'],
            ['lokasi'=>'Unit Sibolga'],
            ['lokasi'=>'Unit Parapat'],
            ['lokasi'=>'Unit Mandailing Natal'],

            // 4. PALEMBANG (14 Anak)
            ['lokasi'=>'Cabang  Palembang'],
            ['lokasi'=>'Cabang Pembantu Pangkal Pinang'],
            ['lokasi'=>'Unit Tanjung Pandan'],
            ['lokasi'=>'Unit Bandar Lampung'],
            ['lokasi'=>'Unit Way Kanan'],
            ['lokasi'=>'Cabang Pembantu Jambi'],
            ['lokasi'=>'Unit Muko Muko'],
            ['lokasi'=>'Unit Kerinci'],
            ['lokasi'=>'Unit Muara Bungo'],
            ['lokasi'=>'Unit Lubuk Linggau'],
            ['lokasi'=>'Cabang Pembantu Bengkulu'],
            ['lokasi'=>'Unit Enggano'],
            ['lokasi'=>'Unit Pagar Alam'],
            ['lokasi'=>'Unit Pekon Serai, Krui Lampung'],

            // 5. YOGYAKARTA (2 Anak)
            ['lokasi'=>'Cabang  Yogyakarta'],
            ['lokasi'=>'Cabang Pembantu Solo'],
            ['lokasi'=>'Unit Cilacap'],

            // 6. SURABAYA (7 Anak)
            ['lokasi'=>'Cabang  Surabaya'],
            ['lokasi'=>'Cabang Pembantu Banyuwangi'],
            ['lokasi'=>'Cabang Pembantu Malang'],
            ['lokasi'=>'Cabang Pembantu Sumenep'],
            ['lokasi'=>'Unit Jember'],
            ['lokasi'=>'Unit Bawean'],
            ['lokasi'=>'Unit Blora'],
            ['lokasi'=>'Unit Kediri'],

            
            // 7. DENPASAR (5 Anak)
            ['lokasi'=>'Cabang  Denpasar'],
            ['lokasi'=>'Cabang Pembantu Labuan Bajo'],
            ['lokasi'=>'Unit Waingapu'],
            ['lokasi'=>'Unit Tambolaka'],
            ['lokasi'=>'Unit Pagerungan'],
            ['lokasi'=>'Unit Buleleng'],

            // 8. BALIKPAPAN (9 Anak)
            ['lokasi'=>'Cabang  Balikpapan'],
            ['lokasi'=>'Cabang Pembantu Samarinda'],
            ['lokasi'=>'Cabang Pembantu Berau'],
            ['lokasi'=>'Unit Datah Dawai'],
            ['lokasi'=>'Unit Melak'],
            ['lokasi'=>'Unit Kota Bangun'],
            ['lokasi'=>'Unit Muara Wahau'],
            ['lokasi'=>'Unit Kutai Timur'],
            ['lokasi'=>'Unit Derawan'],
            ['lokasi'=>'KCP IKN'],

            // 9. SENTANI (30 Anak)
            ['lokasi'=>'Cabang  Sentani'],
            ['lokasi'=>'Cabang Pembantu Biak'],
            ['lokasi'=>'Cabang Pembantu Oksibil'],
            ['lokasi'=>'Cabang Pembantu Timika'],
            ['lokasi'=>'Unit Kiwirok'],
            ['lokasi'=>'Unit Dabra'],
            ['lokasi'=>'Unit Batom'],
            ['lokasi'=>'Unit Senggeh'],
            ['lokasi'=>'Unit Waris (Towe Hitam), Keerom'],
            ['lokasi'=>'Unit Serui'],
            ['lokasi'=>'Unit Numfor'],
            ['lokasi'=>'Unit Kokonao'],
            ['lokasi'=>'Unit Mararena, Sarmi'],
            ['lokasi'=>'Unit Akimuga'],
            ['lokasi'=>'Unit Abmisibil'],
            ['lokasi'=>'Unit Aboy, Peg. Bintang'],
            ['lokasi'=>'Unit Alama, Peg. Bintang'],
            ['lokasi'=>'Unit Jila, Mimika'],
            ['lokasi'=>'Unit Jita, Mimika'],
            ['lokasi'=>'Unit Kapiraya'],
            ['lokasi'=>'Unit Luban'],
            ['lokasi'=>'Unit Okbab'],
            ['lokasi'=>'Unit Potowai, Mimika'],
            ['lokasi'=>'Unit Tsinga, Mimika'],
            ['lokasi'=>'Unit Ubrub, Keerom'],
            ['lokasi'=>'Unit Wangbe, Keerom'],
            ['lokasi'=>'Unit Yuruf, Keerom'],
            ['lokasi'=>'Unit Molof, Keerom'],
            ['lokasi'=>'Unit Lereh, Keerom'],
            ['lokasi'=>'Unit Teraplu'],
            ['lokasi'=>'Unit Kasonaweja'],

            // 10. PONTIANAK (8 Anak)
            ['lokasi'=>'Cabang  Pontianak'],
            ['lokasi'=>'Cabang Pembantu Ketapang'],
            ['lokasi'=>'Unit Sintang'],
            ['lokasi'=>'Unit Putussibau'],
            ['lokasi'=>'Unit Nanga Pinoh'],
            ['lokasi'=>'Unit Sambas'],
            ['lokasi'=>'Unit Harapan, Manis Mata'],
            ['lokasi'=>'Unit Semelagi'],
            ['lokasi'=>'Unit Singkawang'],

            // 11. BANDA ACEH (10 Anak)
            ['lokasi'=>'Cabang  Banda Aceh'],
            ['lokasi'=>'Unit Meulaboh'],
            ['lokasi'=>'Unit Sinabang'],
            ['lokasi'=>'Unit Takengon'],
            ['lokasi'=>'Unit Tapak Tuan'],
            ['lokasi'=>'Unit Sabang'],
            ['lokasi'=>'Unit Singkil'],
            ['lokasi'=>'Unit Kutacane'],
            ['lokasi'=>'Unit Blang Pidi'],
            ['lokasi'=>'Unit Lhok Seumawe'],
            ['lokasi'=>'Unit Gayo Lues'],

            // 12. PEKANBARU (8 Anak)
            ['lokasi'=>'Cabang  Pekanbaru'],
            ['lokasi'=>'Cabang Pembantu Rengat'],
            ['lokasi'=>'Unit Pasir Pangaraian'],
            ['lokasi'=>'Unit Indragiri Hilir'],
            ['lokasi'=>'Unit Dumai'],
            ['lokasi'=>'Unit Pelalawan'],
            ['lokasi'=>'Cabang Pembantu Padang'],
            ['lokasi'=>'Unit Rokot Sipora'],
            ['lokasi'=>'Unit Pasaman Barat'],

            // 13. TANJUNG PINANG (8 Anak)
            ['lokasi'=>'Cabang  Tanjung Pinang'],
            ['lokasi'=>'Cabang Pembantu Batam'],
            ['lokasi'=>'Unit Singkep'],
            ['lokasi'=>'Unit Tanjung Balai Karimun'],
            ['lokasi'=>'Unit Bintan'],
            ['lokasi'=>'Cabang Pembantu Natuna'],
            ['lokasi'=>'Unit Anambas (Tanjung Pinang)'],
            ['lokasi'=>'Unit Matak'],
            ['lokasi'=>'Unit Tambelan'],

            // 14. HALIM (2 Anak)
            ['lokasi'=>'Cabang  Halim'],
            ['lokasi'=>'Cabang Pembantu Curug'],
            ['lokasi'=>'Unit Pondok Cabe'],

            // 15. BANDUNG (4 Anak)
            ['lokasi'=>'Cabang  Bandung'],
            ['lokasi'=>'Cabang Pembantu Cirebon'],
            ['lokasi'=>'Unit Pangandaran'],
            ['lokasi'=>'Unit Tasikmalaya'],
            ['lokasi'=>'Unit Kertajati'],

            // 16. SEMARANG (2 Anak)
            ['lokasi'=>'Cabang  Semarang'],
            ['lokasi'=>'Unit Karimun Jawa'],
            ['lokasi'=>'Unit Purbalingga'],

            // 17. BANJARMASIN (6 Anak)
            ['lokasi'=>'Cabang  Banjarmasin'],
            ['lokasi'=>'Cabang Pembantu Pangkalan Bun'],
            ['lokasi'=>'Cabang Pembantu Sampit'],
            ['lokasi'=>'Unit Kota Baru'],
            ['lokasi'=>'Unit Kuala Pembuang'],
            ['lokasi'=>'Unit Batu Licin'],
            ['lokasi'=>'Unit Tanjung Warukin'],

            // 18. PALANGKARAYA (5 Anak)
            ['lokasi'=>'Cabang  Palangkaraya'],
            ['lokasi'=>'Unit Muara Teweh'],
            ['lokasi'=>'Unit Kuala Kurun'],
            ['lokasi'=>'Unit Buntok'],
            ['lokasi'=>'Unit Tumbang Samba'],
            ['lokasi'=>'Unit Puruk Cahu'],

            // 19. TARAKAN (7 Anak)
            ['lokasi'=>'Cabang  Tarakan'],
            ['lokasi'=>'Cabang Pembantu Malinau'],
            ['lokasi'=>'Unit Nunukan'],
            ['lokasi'=>'Unit Long Bawan'],
            ['lokasi'=>'Unit Long Ampung'],
            ['lokasi'=>'Unit Tanjung Harapan'],
            ['lokasi'=>'Unit Long Layu'],
            ['lokasi'=>'Unit Binuang'],

            // 20. MANADO (17 Anak)
            ['lokasi'=>'Cabang  Manado'],
            ['lokasi'=>'Cabang Pembantu Ternate'],
            ['lokasi'=>'Cabang Pembantu Gorontalo'],
            ['lokasi'=>'Unit Labuha'],
            ['lokasi'=>'Unit Morotai'],
            ['lokasi'=>'Unit Melonguane'],
            ['lokasi'=>'Unit Kao'],
            ['lokasi'=>'Unit Galela'],
            ['lokasi'=>'Unit Buli Maba'],
            ['lokasi'=>'Unit Sanana'],
            ['lokasi'=>'Unit Tahuna'],
            ['lokasi'=>'Unit Halmahera Tengah'],
            ['lokasi'=>'Unit Manggole Kep. Sola'],
            ['lokasi'=>'Unit Miangas'],
            ['lokasi'=>'Unit Siau'],
            ['lokasi'=>'Unit Bolaang Mongondow'],
            ['lokasi'=>'Unit Pohuwato'],
            ['lokasi'=>'Unit Weda Tengah'],

            // 21. KENDARI (6 Anak)
            ['lokasi'=>'Cabang  Kendari'],
            ['lokasi'=>'Unit Wakatobi'],
            ['lokasi'=>'Unit Bau Bau'],
            ['lokasi'=>'Unit Kolaka'],
            ['lokasi'=>'Unit Sugimanuru'],
            ['lokasi'=>'Unit Morowali'],
            ['lokasi'=>'Unit Bahodopi'],

            // 22. LOMBOK (4 Anak)
            ['lokasi'=>'Cabang  Lombok'],
            ['lokasi'=>'Cabang Pembantu Bima'],
            ['lokasi'=>'Cabang Pembantu Sumbawa'],
            ['lokasi'=>'Unit Lunyuk, Sumbawa'],
            ['lokasi'=>'Unit Poto Tano'],

            // 23. KUPANG (11 Anak)
            ['lokasi'=>'Cabang  Kupang'],
            ['lokasi'=>'Cabang Pembantu Ende'],
            ['lokasi'=>'Unit Maumere'],
            ['lokasi'=>'Unit Larantuka'],
            ['lokasi'=>'Unit Rote'],
            ['lokasi'=>'Unit Sabu'],
            ['lokasi'=>'Unit Bajawa'],
            ['lokasi'=>'Unit Lewoleba'],
            ['lokasi'=>'Unit Atambua'],
            ['lokasi'=>'Unit Ruteng'],
            ['lokasi'=>'Unit Alor'],
            ['lokasi'=>'Unit Pantar'],

            // 24. AMBON (12 Anak)
            ['lokasi'=>'Cabang  Ambon'],
            ['lokasi'=>'Cabang Pembantu Tual, Karel Sadsuitubun'],
            ['lokasi'=>'Unit Dobo'],
            ['lokasi'=>'Unit Saumlaki'],
            ['lokasi'=>'Unit Bandanaira'],
            ['lokasi'=>'Unit Namrole'],
            ['lokasi'=>'Unit Larat'],
            ['lokasi'=>'Unit Wahai'],
            ['lokasi'=>'Unit Amahai'],
            ['lokasi'=>'Unit Moa'],
            ['lokasi'=>'Unit Kuffar'],
            ['lokasi'=>'Unit Namlea'],
            ['lokasi'=>'Unit Kisar'],

            // 25. WAMENA (21 Anak)
            ['lokasi'=>'Cabang  Wamena'],
            ['lokasi'=>'Unit Tiom'],
            ['lokasi'=>'Unit Karubaga'],
            ['lokasi'=>'Unit Bokondini'],
            ['lokasi'=>'Unit Nop Goliat Dekai, Yahukimo'],
            ['lokasi'=>'Unit Elelim'],
            ['lokasi'=>'Unit Anggruk'],
            ['lokasi'=>'Unit Yalimo'],
            ['lokasi'=>'Unit Holuwun'],
            ['lokasi'=>'Unit Mamberamo Tengah'],
            ['lokasi'=>'Unit Mamit'],
            ['lokasi'=>'Unit Ninia'],
            ['lokasi'=>'Unit Pasema'],
            ['lokasi'=>'Unit Sobaham'],
            ['lokasi'=>'Unit Silimo'],
            ['lokasi'=>'Unit Suru-Suru'],
            ['lokasi'=>'Unit Tolikara'],
            ['lokasi'=>'Unit Mapnduma, Nduga'],
            ['lokasi'=>'Unit Mugi, Nduga'],
            ['lokasi'=>'Unit Paro, Nduga'],
            ['lokasi'=>'Unit Mamberamo Raya'],
            ['lokasi'=>'Unit Kenyam, Nduga'],

            // 26. NABIRE (16 Anak)
            ['lokasi'=>'Cabang  Nabire'],
            ['lokasi'=>'Unit Illaga'],
            ['lokasi'=>'Unit Bilorai'],
            ['lokasi'=>'Unit Mulia'],
            ['lokasi'=>'Unit Moanamani'],
            ['lokasi'=>'Unit Enarotali'],
            ['lokasi'=>'Unit Waghete, Paniai'],
            ['lokasi'=>'Unit Illu'],
            ['lokasi'=>'Unit Sinak'],
            ['lokasi'=>'Unit Aboyaga, Nabire'],
            ['lokasi'=>'Unit Duma'],
            ['lokasi'=>'Unit Obano, Paniai'],
            // 👇 INI YANG PENTING: Ada 2 Obano berbeda di file CSV kamu!
            // Satu di Paniai, satu di Intan Jaya. Harus dikasih ID beda.
            ['lokasi'=>'Unit Obano, Intan Jaya'], 
            ['lokasi'=>'Unit Botawa'],
            ['lokasi'=>'Unit Beoga, Intan Jaya'],
            ['lokasi'=>'Unit Bilai, Intan Jaya'],
            ['lokasi'=>'Unit Puncak Jaya'],

            // 27. SORONG (18 Anak)
            ['lokasi'=>'Cabang  Sorong'],
            ['lokasi'=>'Cabang Pembantu Manokwari'],
            ['lokasi'=>'Unit Babo'],
            ['lokasi'=>'Unit Bintuni'],
            ['lokasi'=>'Unit Fak Fak'],
            ['lokasi'=>'Unit Kaimana'],
            ['lokasi'=>'Unit Anggi'],
            ['lokasi'=>'Unit Ayawasi'],
            ['lokasi'=>'Unit Kambuaya'],
            ['lokasi'=>'Unit Inanwatan'],
            ['lokasi'=>'Unit Marinda, Waisai, Raja Ampat'],
            ['lokasi'=>'Unit Teminabuan'],
            ['lokasi'=>'Unit Kebar'],
            ['lokasi'=>'Unit Merdey, Teluk Bintuni'],
            ['lokasi'=>'Unit Kabare'],
            ['lokasi'=>'Unit Wasior'],
            ['lokasi'=>'Unit Ransiki'],
            ['lokasi'=>'Unit Werur, Tambrauw, Papua Barat'],
            ['lokasi'=>'Unit Segun, Sorong'],

            // 28. MERAUKE (19 Anak)
            ['lokasi'=>'Cabang  Merauke'],
            ['lokasi'=>'Cabang Pembantu Tanah Merah'],
            ['lokasi'=>'Unit Ewer'],
            ['lokasi'=>'Unit Kepi'],
            ['lokasi'=>'Unit Bade'],
            ['lokasi'=>'Unit Kimam'],
            ['lokasi'=>'Unit Okaba'],
            ['lokasi'=>'Unit Mindiptanah'],
            ['lokasi'=>'Unit Kamur'],
            ['lokasi'=>'Unit Bomakia'],
            ['lokasi'=>'Unit Senggo'],
            ['lokasi'=>'Unit Manggelum'],
            ['lokasi'=>'Unit Yaniruma'],
            ['lokasi'=>'Unit Wanggemalo'],
            ['lokasi'=>'Unit Iwur'],
            ['lokasi'=>'Unit Aboge'],
            ['lokasi'=>'Unit Wanam'],
            ['lokasi'=>'Unit Borome'],
            ['lokasi'=>'Unit Kebo, Paniai'],
            ['lokasi'=>'Unit Kilmit'],
        ];

        $this->command->info('Memulai proses seeding...');

        $currentParentId = null;

        foreach ($rawData as $row) {
            $rawName = $row['lokasi'];
            $normalized = $this->normalizeName($rawName);
            
            $level = 'unit';
            $cleanName = $normalized;
            $id = null;
            $coordinates = null;
            $city = null;
            $provinsi = 'Indonesia';

            // --- IDENTIFIKASI ---
            if (array_key_exists($normalized, $this->cabangUtamaMap)) {
                $level = 'cabang_utama';
                $id = $this->cabangUtamaMap[$normalized];
                $cleanName = str_replace('Cabang ', '', $normalized);
                $currentParentId = $id; 
                
                if (isset($this->airportDetails[$id])) {
                    $detail = $this->airportDetails[$id];
                    $coordinates = $detail['coords']; 
                    $city = $detail['city'];
                    $provinsi = $detail['provinsi'];
                }
            }
            elseif (Str::startsWith($normalized, 'Cabang ') && !Str::contains($normalized, 'Pembantu')) {
                $level = 'cabang_utama';
                $cleanName = str_replace('Cabang ', '', $normalized);
                $id = strtoupper(substr($cleanName, 0, 3)); 
                $currentParentId = $id; 
            }
            elseif (Str::startsWith($normalized, 'Cabang Pembantu')) {
                $level = 'cabang_pembantu';
                $cleanName = trim(str_replace('Cabang Pembantu', '', $normalized));
            }
            elseif (Str::startsWith($normalized, 'Unit')) {
                $level = 'unit';
                $cleanName = trim(str_replace('Unit', '', $normalized));
            }

            $finalParentId = ($level === 'cabang_utama') ? null : $currentParentId;

            // --- ID GENERATION & DUPLICATE HANDLING ---
            if (!$id) {
                // Generate base suffix (3 huruf)
                $suffix = strtoupper(substr(str_replace([' ', ','], '', $cleanName), 0, 3));
                
                // Khusus kasus "Obano" yang ada 2 (Paniai & Intan Jaya)
                if (Str::contains($normalized, 'Intan Jaya')) {
                    $suffix = 'ITJ'; 
                } elseif (Str::contains($normalized, 'Paniai')) {
                    $suffix = 'PNI';
                }

                $prefix = $finalParentId ? $finalParentId . '-' : 'UNIT-';
                $id = $prefix . $suffix;

                // Cek collision (jika masih ada ID kembar, tambahkan random number)
                $counter = 1;
                while (Airport::where('id', $id)->exists()) {
                    $id = $prefix . $suffix . $counter;
                    $counter++;
                }
            }

            if (!$city) {
                $city = $cleanName;
            }

            Airport::updateOrCreate(
                ['id' => $id],
                [
                    'name' => $normalized,
                    'city' => $city,
                    'provinsi' => $provinsi,
                    'level' => $level,
                    'parent_id' => $finalParentId, 
                    'coordinates' => $coordinates,
                    'is_active' => true
                ]
            );
        }
        
        $this->command->info('✅ Database berhasil diperbarui! Total 301 data.');
    }

    private function normalizeName($name)
    {
        return trim(preg_replace('/\s+/', ' ', $name));
    }
}
<?php

namespace Database\Seeders;

use App\Models\Airport;
use Illuminate\Database\Seeder;

class AirportSeeder extends Seeder
{
    public function run()
    {
        // Data Detail hanya untuk 28 Cabang Utama (yang butuh marker)
        // Kolom 'safetyReport' SUDAH DIHAPUS dari sini agar database bersih.
        $airportData = [
            ['id' => 'JATSC', 'name' => 'JATSC', 'city' => 'Tangerang', 'provinsi' => 'Banten', 'coordinates' => [-6.1255, 106.6558]],
            ['id' => 'MATSC', 'name' => 'MATSC', 'city' => 'Makassar', 'provinsi' => 'Sulawesi Selatan', 'coordinates' => [-5.0616, 119.5540]],
            ['id' => 'KNO', 'name' => 'Kualanamu', 'city' => 'Deli Serdang', 'provinsi' => 'Sumatera Utara', 'coordinates' => [3.6422, 98.8852]],
            ['id' => 'PLM', 'name' => 'Palembang', 'city' => 'Palembang', 'provinsi' => 'Sumatera Selatan', 'coordinates' => [-2.8980, 104.7006]],
            ['id' => 'YIA', 'name' => 'Yogyakarta', 'city' => 'Kulon Progo', 'provinsi' => 'DIY', 'coordinates' => [-7.9075, 110.0544]],
            ['id' => 'SUB', 'name' => 'Surabaya', 'city' => 'Sidoarjo', 'provinsi' => 'Jawa Timur', 'coordinates' => [-7.3798, 112.7868]],
            ['id' => 'DPS', 'name' => 'Denpasar', 'city' => 'Denpasar', 'provinsi' => 'Bali', 'coordinates' => [-8.7481, 115.1671]],
            ['id' => 'BPN', 'name' => 'Balikpapan', 'city' => 'Balikpapan', 'provinsi' => 'Kalimantan Timur', 'coordinates' => [-1.2682, 116.8944]],
            ['id' => 'DJJ', 'name' => 'Sentani', 'city' => 'Jayapura', 'provinsi' => 'Papua', 'coordinates' => [-2.5768, 140.5156]],
            ['id' => 'PNK', 'name' => 'Pontianak', 'city' => 'Pontianak', 'provinsi' => 'Kalimantan Barat', 'coordinates' => [-0.1494, 109.4036]],
            ['id' => 'BTJ', 'name' => 'Banda Aceh', 'city' => 'Banda Aceh', 'provinsi' => 'Aceh', 'coordinates' => [5.5244, 95.4170]],
            ['id' => 'PKU', 'name' => 'Pekanbaru', 'city' => 'Pekanbaru', 'provinsi' => 'Riau', 'coordinates' => [0.4605, 101.4443]],
            ['id' => 'TNJ', 'name' => 'Tanjung Pinang', 'city' => 'Tanjung Pinang', 'provinsi' => 'Kepulauan Riau', 'coordinates' => [0.9238, 104.5293]],
            ['id' => 'HLP', 'name' => 'Halim', 'city' => 'Jakarta Timur', 'provinsi' => 'DKI Jakarta', 'coordinates' => [-6.2657, 106.8906]],
            ['id' => 'BDO', 'name' => 'Bandung', 'city' => 'Bandung', 'provinsi' => 'Jawa Barat', 'coordinates' => [-6.9006, 107.5763]],
            ['id' => 'SRG', 'name' => 'Semarang', 'city' => 'Semarang', 'provinsi' => 'Jawa Tengah', 'coordinates' => [-6.9730, 110.3755]],
            ['id' => 'BDJ', 'name' => 'Banjarmasin', 'city' => 'Banjarbaru', 'provinsi' => 'Kalimantan Selatan', 'coordinates' => [-3.4474, 114.7617]],
            ['id' => 'PKY', 'name' => 'Palangkaraya', 'city' => 'Palangkaraya', 'provinsi' => 'Kalimantan Tengah', 'coordinates' => [-2.2246, 113.9439]],
            ['id' => 'TRK', 'name' => 'Tarakan', 'city' => 'Tarakan', 'provinsi' => 'Kalimantan Utara', 'coordinates' => [3.3263, 117.5652]],
            ['id' => 'MDC', 'name' => 'Manado', 'city' => 'Manado', 'provinsi' => 'Sulawesi Utara', 'coordinates' => [1.5342, 124.9252]],
            ['id' => 'KDI', 'name' => 'Kendari', 'city' => 'Kendari', 'provinsi' => 'Sulawesi Tenggara', 'coordinates' => [-4.0833, 122.4166]],
            ['id' => 'LOP', 'name' => 'Lombok', 'city' => 'Lombok Tengah', 'provinsi' => 'Nusa Tenggara Barat', 'coordinates' => [-8.7602, 116.2730]],
            ['id' => 'KOE', 'name' => 'Kupang', 'city' => 'Kupang', 'provinsi' => 'Nusa Tenggara Timur', 'coordinates' => [-10.1764, 123.6669]],
            ['id' => 'AMQ', 'name' => 'Ambon', 'city' => 'Ambon', 'provinsi' => 'Maluku', 'coordinates' => [-3.7075, 128.0894]],
            ['id' => 'BIK', 'name' => 'Biak', 'city' => 'Biak', 'provinsi' => 'Papua', 'coordinates' => [-1.1923, 136.1089]],
            ['id' => 'MKQ', 'name' => 'Merauke', 'city' => 'Merauke', 'provinsi' => 'Papua Selatan', 'coordinates' => [-8.5203, 140.4192]],
            ['id' => 'SOQ', 'name' => 'Sorong', 'city' => 'Sorong', 'provinsi' => 'Papua Barat Daya', 'coordinates' => [-0.9255, 131.2882]],
            ['id' => 'PGK', 'name' => 'Pangkal Pinang', 'city' => 'Pangkal Pinang', 'provinsi' => 'Bangka Belitung', 'coordinates' => [-2.1613, 106.1384]],
            ['id' => 'DJB', 'name' => 'Jambi', 'city' => 'Jambi', 'provinsi' => 'Jambi', 'coordinates' => [-1.6424, 103.6449]],
            ['id' => 'PDG', 'name' => 'Padang', 'city' => 'Padang Pariaman', 'provinsi' => 'Sumatera Barat', 'coordinates' => [-0.7858, 100.2807]],
            ['id' => 'SOC', 'name' => 'Solo', 'city' => 'Boyolali', 'provinsi' => 'Jawa Tengah', 'coordinates' => [-7.5161, 110.7552]],
            ['id' => 'AAP', 'name' => 'Samarinda', 'city' => 'Samarinda', 'provinsi' => 'Kalimantan Timur', 'coordinates' => [-0.3725, 117.2494]],
            ['id' => 'PLW', 'name' => 'Palu', 'city' => 'Palu', 'provinsi' => 'Sulawesi Tengah', 'coordinates' => [-0.9179, 119.9099]],
            ['id' => 'GTO', 'name' => 'Gorontalo', 'city' => 'Gorontalo', 'provinsi' => 'Gorontalo', 'coordinates' => [0.6372, 122.8447]],
            ['id' => 'TTE', 'name' => 'Ternate', 'city' => 'Ternate', 'provinsi' => 'Maluku Utara', 'coordinates' => [0.8322, 127.3789]],
            ['id' => 'WMX', 'name' => 'Wamena', 'city' => 'Wamena', 'provinsi' => 'Papua Pegunungan', 'coordinates' => [-4.1008, 138.9567]],
            ['id' => 'NBX', 'name' => 'Nabire', 'city' => 'Nabire', 'provinsi' => 'Papua Tengah', 'coordinates' => [-3.3592, 135.4856]],
        ];

        foreach ($airportData as $data) {
            // Gunakan updateOrCreate:
            // Jika ID 'AMQ' sudah ada -> Update kolom city, provinsi, coords
            // Jika ID belum ada -> Buat baru (termasuk name)
            Airport::updateOrCreate(
                ['id' => $data['id']], 
                [
                    'name' => $data['name'], 
                    'city' => $data['city'],
                    'provinsi' => $data['provinsi'],
                    'coordinates' => $data['coordinates'],
                ]
            );
        }
    }
}
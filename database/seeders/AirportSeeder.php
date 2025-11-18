<?php

namespace Database\Seeders;

use App\Models\Airport; // <-- TAMBAHKAN BARIS INI
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AirportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $airportData = [
          [
            "id" => "ARD",
            "name" => "Alor",
            "city" => "Alor",
            "provinsi" => "Nusa Tenggara Timur",
            "coordinates" => [-8.217, 124.571], // Bandara Mali
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "AMQ",
            "name" => "Ambon",
            "city" => "Ambon",
            "provinsi" => "Maluku",
            "coordinates" => [-3.7075, 128.0894], // Bandara Pattimura
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "ABU",
            "name" => "Atambua",
            "city" => "Atambua",
            "provinsi" => "Nusa Tenggara Timur",
            "coordinates" => [-9.0969, 124.9080], // Bandara A. A. Bere Tallo
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "BHD",
            "name" => "Bahodopi",
            "city" => "Bahodopi",
            "provinsi" => "Sulawesi Tengah",
            "coordinates" => [-2.859, 122.146], // Bandara Bahodopi (IMIP)
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "BJW",
            "name" => "Bajawa",
            "city" => "Bajawa",
            "provinsi" => "Nusa Tenggara Timur",
            "coordinates" => [-8.5133, 121.0664], // Bandara Bajawa Soa
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "BPN",
            "name" => "Balikpapan",
            "city" => "Balikpapan",
            "provinsi" => "Kalimantan Timur",
            "coordinates" => [-1.2683, 116.8944], // Bandara Sepinggan
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "BTJ",
            "name" => "Banda Aceh",
            "city" => "Banda Aceh",
            "provinsi" => "Aceh",
            "coordinates" => [5.5202, 95.4209], // Bandara Sultan Iskandar Muda
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "TKG",
            "name" => "Bandar Lampung",
            "city" => "Bandar Lampung",
            "provinsi" => "Lampung",
            "coordinates" => [-5.2425, 105.1788], // Bandara Radin Inten II
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "BDO",
            "name" => "Bandung",
            "city" => "Bandung",
            "provinsi" => "Jawa Barat",
            "coordinates" => [-6.9015, 107.5750], // Bandara Husein Sastranegara
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "BDJ",
            "name" => "Banjarmasin",
            "city" => "Banjarmasin",
            "provinsi" => "Kalimantan Selatan",
            "coordinates" => [-3.4422, 114.7625], // Bandara Syamsudin Noor
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "BWX",
            "name" => "Banyuwangi",
            "city" => "Banyuwangi",
            "provinsi" => "Jawa Timur",
            "coordinates" => [-8.3106, 114.3401], // Bandara Banyuwangi
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "BTH",
            "name" => "Batam",
            "city" => "Batam",
            "provinsi" => "Kepulauan Riau",
            "coordinates" => [1.1234, 104.1153], // Bandara Hang Nadim
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "BUW",
            "name" => "Bau Bau",
            "city" => "Bau-Bau",
            "provinsi" => "Sulawesi Tenggara",
            "coordinates" => [-5.5166, 122.5500], // Bandara Betoambari
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "BKS",
            "name" => "Bengkulu",
            "city" => "Bengkulu",
            "provinsi" => "Bengkulu",
            "coordinates" => [-3.8619, 102.3366], // Bandara Fatmawati Soekarno
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "BEJ",
            "name" => "Berau",
            "city" => "Berau",
            "provinsi" => "Kalimantan Timur",
            "coordinates" => [2.1552, 117.4322], // Bandara Kalimarau
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "BIK",
            "name" => "Biak",
            "city" => "Biak",
            "provinsi" => "Papua",
            "coordinates" => [-1.1900, 136.1075], // Bandara Frans Kaisiepo
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "BMU",
            "name" => "Bima",
            "city" => "Bima",
            "provinsi" => "Nusa Tenggara Barat",
            "coordinates" => [-8.5397, 118.6872], // Bandara Sultan Muhammad Salahuddin
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "CXP",
            "name" => "Cilacap",
            "city" => "Cilacap",
            "provinsi" => "Jawa Tengah",
            "coordinates" => [-7.6450, 109.0341], // Bandara Tunggul Wulung
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "CBN",
            "name" => "Cirebon",
            "city" => "Cirebon",
            "provinsi" => "Jawa Barat",
            "coordinates" => [-6.7561, 108.5308], // Bandara Cakrabuana (Penggung)
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "CBJ",
            "name" => "Curug",
            "city" => "Curug",
            "provinsi" => "Banten",
            "coordinates" => [-6.2909, 106.5661], // Bandara Budiarto
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "DPS",
            "name" => "Denpasar",
            "city" => "Denpasar",
            "provinsi" => "Bali",
            "coordinates" => [-8.7475, 115.1691], // Bandara Ngurah Rai
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "DOB",
            "name" => "Dobo",
            "city" => "Dobo",
            "provinsi" => "Maluku",
            "coordinates" => [-5.7716, 134.2125], // Bandara Rar Gwamar
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "ENE",
            "name" => "Ende",
            "city" => "Ende",
            "provinsi" => "Nusa Tenggara Timur",
            "coordinates" => [-8.8492, 121.6606], // Bandara H. Hasan Aroeboesman
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "EWE",
            "name" => "Ewer",
            "city" => "Ewer",
            "provinsi" => "Papua Selatan",
            "coordinates" => [-5.6738, 138.2047], // Bandara Ewer
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "FKQ",
            "name" => "Fak Fak",
            "city" => "Fak Fak",
            "provinsi" => "Papua Barat",
            "coordinates" => [-2.9200, 132.2669], // Bandara Torea
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "GTO",
            "name" => "Gorontalo",
            "city" => "Gorontalo",
            "provinsi" => "Gorontalo",
            "coordinates" => [0.6380, 122.8522], // Bandara Djalaluddin
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "GNS",
            "name" => "Gunung Sitoli",
            "city" => "Gunung Sitoli",
            "provinsi" => "Sumatera Utara",
            "coordinates" => [1.1655, 97.7061], // Bandara Binaka
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "HLP",
            "name" => "Halim",
            "city" => "Jakarta",
            "provinsi" => "DKI Jakarta",
            "coordinates" => [-6.2662, 106.8897], // Bandara Halim Perdanakusuma
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "ILX",
            "name" => "Illaga",
            "city" => "Ilaga",
            "provinsi" => "Papua Tengah",
            "coordinates" => [-3.9996, 137.6528], // Bandara Aminggaru
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "DJB",
            "name" => "Jambi",
            "city" => "Jambi",
            "provinsi" => "Jambi",
            "coordinates" => [-1.6358, 103.6408], // Bandara Sultan Thaha
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "JATSC",
            "name" => "JATSC",
            "city" => "Tangerang",
            "provinsi" => "Banten",
            "coordinates" => [-6.1268, 106.6531], // Jakarta Air Traffic Service Center (di Bandara Soetta)
            "safetyReport" => ["rating" => "A", "lastInspection" => "2025-01-01", "notes" => "Pusat Kontrol Udara Jakarta."]
          ],
          [
            "id" => "KNG",
            "name" => "Kaimana",
            "city" => "Kaimana",
            "provinsi" => "Papua Barat",
            "coordinates" => [-3.6444, 133.6952], // Bandara Utarom
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "KTR-PUSAT",
            "name" => "Kantor Pusat",
            "city" => "Tangerang",
            "provinsi" => "Banten",
            "coordinates" => [-6.1517, 106.6630], // Gedung Kantor Pusat AirNav Indonesia
            "safetyReport" => ["rating" => "A", "lastInspection" => "2025-01-01", "notes" => "Head Office AirNav Indonesia."]
          ],
          [
            "id" => "KWB",
            "name" => "Karimun Jawa",
            "city" => "Karimun Jawa",
            "provinsi" => "Jawa Tengah",
            "coordinates" => [-5.8011, 110.4786], // Bandara Dewadaru
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "DHX",
            "name" => "Kediri",
            "city" => "Kediri",
            "provinsi" => "Jawa Timur",
            "coordinates" => [-7.9100, 112.1930], // Bandara Dhoho
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "KDI",
            "name" => "Kendari",
            "city" => "Kendari",
            "provinsi" => "Sulawesi Tenggara",
            "coordinates" => [-4.0816, 122.4182], // Bandara Haluoleo
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "KJT",
            "name" => "Kertajati",
            "city" => "Majalengka",
            "provinsi" => "Jawa Barat",
            "coordinates" => [-6.6664, 108.1783], // Bandara Internasional Kertajati
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "KTG",
            "name" => "Ketapang",
            "city" => "Ketapang",
            "provinsi" => "Kalimantan Barat",
            "coordinates" => [-1.8163, 109.9633], // Bandara Rahadi Oesman
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "KKA",
            "name" => "Kolaka",
            "city" => "Kolaka",
            "provinsi" => "Sulawesi Tenggara",
            "coordinates" => [-4.3411, 121.5238], // Bandara Sangia Nibandera
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "KBU",
            "name" => "Kota Baru",
            "city" => "Kotabaru",
            "provinsi" => "Kalimantan Selatan",
            "coordinates" => [-3.3033, 116.1664], // Bandara Gusti Sjamsir Alam
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "KOE",
            "name" => "Kupang",
            "city" => "Kupang",
            "provinsi" => "Nusa Tenggara Timur",
            "coordinates" => [-10.1713, 123.6711], // Bandara El Tari
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "LBJ",
            "name" => "Labuan Bajo",
            "city" => "Labuan Bajo",
            "provinsi" => "Nusa Tenggara Timur",
            "coordinates" => [-8.4866, 119.8891], // Bandara Komodo
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "LAH",
            "name" => "Labuha",
            "city" => "Labuha",
            "provinsi" => "Maluku Utara",
            "coordinates" => [-0.6373, 127.5007], // Bandara Oesman Sadik
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "LKA",
            "name" => "Larantuka",
            "city" => "Larantuka",
            "provinsi" => "Nusa Tenggara Timur",
            "coordinates" => [-8.2831, 122.9567], // Bandara Gewayantana
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "LSE",
            "name" => "Lasondre",
            "city" => "Pulau-Pulau Batu",
            "provinsi" => "Sumatera Utara",
            "coordinates" => [-0.0186, 98.2991], // Bandara Lasondre
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "LWE",
            "name" => "Lewoleba",
            "city" => "Lewoleba",
            "provinsi" => "Nusa Tenggara Timur",
            "coordinates" => [-8.3625, 123.4380], // Bandara Wunopito
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "LOP",
            "name" => "Lombok",
            "city" => "Praya",
            "provinsi" => "Nusa Tenggara Barat",
            "coordinates" => [-8.7561, 116.2769], // Bandara Internasional Lombok
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "LPU",
            "name" => "Long Ampung",
            "city" => "Long Apung",
            "provinsi" => "Kalimantan Utara",
            "coordinates" => [1.7036, 114.9702], // Bandara Long Apung
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "LUW",
            "name" => "Luwuk",
            "city" => "Luwuk",
            "provinsi" => "Sulawesi Tengah",
            "coordinates" => [-1.0503, 122.7561], // Bandara Syukuran Aminuddin Amir
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "MLG",
            "name" => "Malang",
            "city" => "Malang",
            "provinsi" => "Jawa Timur",
            "coordinates" => [-7.9283, 112.7133], // Bandara Abdul Rachman Saleh
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "LNU",
            "name" => "Malinau",
            "city" => "Malinau",
            "provinsi" => "Kalimantan Utara",
            "coordinates" => [2.8530, 116.6347], // Bandara Robert Atty Bessing
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "MJU",
            "name" => "Mamuju",
            "city" => "Mamuju",
            "provinsi" => "Sulawesi Barat",
            "coordinates" => [-2.5866, 119.0291], // Bandara Tampa Padang
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "MDC",
            "name" => "Manado",
            "city" => "Manado",
            "provinsi" => "Sulawesi Utara",
            "coordinates" => [1.5491, 124.9263], // Bandara Sam Ratulangi
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "MKW",
            "name" => "Manokwari",
            "city" => "Manokwari",
            "provinsi" => "Papua Barat",
            "coordinates" => [-0.8916, 134.0491], // Bandara Rendani
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "RJM",
            "name" => "Marinda, Waisai, Raj",
            "city" => "Raja Ampat",
            "provinsi" => "Papua Barat Daya",
            "coordinates" => [-0.4319, 130.7719], // Bandara Marinda
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "MATSC",
            "name" => "MATSC",
            "city" => "Makassar",
            "provinsi" => "Sulawesi Selatan",
            "coordinates" => [-5.0620, 119.5516], // Makassar Air Traffic Service Center (di Bandara Hasanuddin)
            "safetyReport" => ["rating" => "A", "lastInspection" => "2025-01-01", "notes" => "Pusat Kontrol Udara Makassar."]
          ],
          [
            "id" => "MOF",
            "name" => "Maumere",
            "city" => "Maumere",
            "provinsi" => "Nusa Tenggara Timur",
            "coordinates" => [-8.6408, 122.2368], // Bandara Frans Seda
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "KNO",
            "name" => "Medan",
            "city" => "Deli Serdang",
            "provinsi" => "Sumatera Utara",
            "coordinates" => [3.6422, 98.8852], // Bandara Kualanamu
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "MKQ",
            "name" => "Merauke",
            "city" => "Merauke",
            "provinsi" => "Papua Selatan",
            "coordinates" => [-8.5202, 140.4183], // Bandara Mopah
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "MEQ",
            "name" => "Meulaboh",
            "city" => "Meulaboh",
            "provinsi" => "Aceh",
            "coordinates" => [4.1350, 96.2575], // Bandara Cut Nyak Dhien
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "OTI",
            "name" => "Morotai",
            "city" => "Morotai",
            "provinsi" => "Maluku Utara",
            "coordinates" => [2.0622, 128.3275], // Bandara Pitu
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "MOH",
            "name" => "Morowali",
            "city" => "Morowali",
            "provinsi" => "Sulawesi Tengah",
            "coordinates" => [-2.2033, 121.6602], // Bandara Maleo
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "MRB",
            "name" => "Muara Bungo",
            "city" => "Muara Bungo",
            "provinsi" => "Jambi",
            "coordinates" => [-1.5425, 102.1827], // Bandara Muara Bungo
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "NBX",
            "name" => "Nabire",
            "city" => "Nabire",
            "provinsi" => "Papua Tengah",
            "coordinates" => [-3.4007, 135.3967], // Bandara Douw Aturure
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "NAM",
            "name" => "Namlea",
            "city" => "Namlea",
            "provinsi" => "Maluku",
            "coordinates" => [-3.2369, 127.1002], // Bandara Namniwel
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "NRE",
            "name" => "Namrole",
            "city" => "Namrole",
            "provinsi" => "Maluku",
            "coordinates" => [-3.8558, 126.6997], // Bandara Namrole
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "OKL",
            "name" => "Oksibil",
            "city" => "Oksibil",
            "provinsi" => "Papua Pegunungan",
            "coordinates" => [-4.8980, 140.6186], // Bandara Oksibil
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "PDG",
            "name" => "Padang",
            "city" => "Padang Pariaman",
            "provinsi" => "Sumatera Barat",
            "coordinates" => [-0.7866, 100.2805], // Bandara Internasional Minangkabau
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "PKY",
            "name" => "Palangkaraya",
            "city" => "Palangkaraya",
            "provinsi" => "Kalimantan Tengah",
            "coordinates" => [-2.2250, 113.9425], // Bandara Tjilik Riwut
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "PLM",
            "name" => "Palembang",
            "city" => "Palembang",
            "provinsi" => "Sumatera Selatan",
            "coordinates" => [-2.9002, 104.7000], // Bandara Sultan Mahmud Badaruddin II
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "PLW",
            "name" => "Palu",
            "city" => "Palu",
            "provinsi" => "Sulawesi Tengah",
            "coordinates" => [-0.9186, 119.9097], // Bandara Mutiara SIS Al-Jufri
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "CJN",
            "name" => "Pangandaran",
            "city" => "Pangandaran",
            "provinsi" => "Jawa Barat",
            "coordinates" => [-7.7200, 108.4886], // Bandara Nusawiru
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "PGK",
            "name" => "Pangkal Pinang",
            "city" => "Pangkal Pinang",
            "provinsi" => "Bangka Belitung",
            "coordinates" => [-2.1619, 106.1388], // Bandara Depati Amir
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "PKN",
            "name" => "Pangkalan Bun",
            "city" => "Pangkalan Bun",
            "provinsi" => "Kalimantan Tengah",
            "coordinates" => [-2.7058, 111.6708], // Bandara Iskandar
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "PPR",
            "name" => "Pasir Pangaraian",
            "city" => "Pasir Pangaraian",
            "provinsi" => "Riau",
            "coordinates" => [0.8454, 100.3698], // Bandara Tuanku Tambusai
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "PKU",
            "name" => "Pekanbaru",
            "city" => "Pekanbaru",
            "provinsi" => "Riau",
            "coordinates" => [0.4642, 101.4465], // Bandara Sultan Syarif Kasim II
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "PNK",
            "name" => "Pontianak",
            "city" => "Pontianak",
            "provinsi" => "Kalimantan Barat",
            "coordinates" => [-0.1505, 109.4038], // Bandara Supadio
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "PSU",
            "name" => "Putussibau",
            "city" => "Putussibau",
            "provinsi" => "Kalimantan Barat",
            "coordinates" => [0.8355, 112.9369], // Bandara Pangsuma
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "RGT",
            "name" => "Rengat",
            "city" => "Rengat",
            "provinsi" => "Riau",
            "coordinates" => [-0.3547, 102.3242], // Bandara Japura
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "RTG",
            "name" => "Ruteng",
            "city" => "Ruteng",
            "provinsi" => "Nusa Tenggara Timur",
            "coordinates" => [-8.5970, 120.4770], // Bandara Frans Sales Lega
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "SAU",
            "name" => "Sabu",
            "city" => "Sabu",
            "provinsi" => "Nusa Tenggara Timur",
            "coordinates" => [-10.4908, 121.8417], // Bandara Tardamu
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "AAP",
            "name" => "Samarinda",
            "city" => "Samarinda",
            "provinsi" => "Kalimantan Timur",
            "coordinates" => [-0.3755, 117.2513], // Bandara APT Pranoto
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "SMQ",
            "name" => "Sampit",
            "city" => "Sampit",
            "provinsi" => "Kalimantan Tengah",
            "coordinates" => [-2.4991, 112.9750], // Bandara H. Asan
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "SXK",
            "name" => "Saumlaki",
            "city" => "Saumlaki",
            "provinsi" => "Maluku",
            "coordinates" => [-7.9869, 131.3061], // Bandara Mathias Kilmaskossu
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "SRG",
            "name" => "Semarang",
            "city" => "Semarang",
            "provinsi" => "Jawa Tengah",
            "coordinates" => [-6.9713, 110.3741], // Bandara Jenderal Ahmad Yani
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "DJJ",
            "name" => "Sentani",
            "city" => "Jayapura",
            "provinsi" => "Papua",
            "coordinates" => [-2.5769, 140.5161], // Bandara Sentani
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "FLZ",
            "name" => "Sibolga",
            "city" => "Sibolga",
            "provinsi" => "Sumatera Utara",
            "coordinates" => [1.5558, 98.8888], // Bandara Ferdinand Lumban Tobing
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "DTB",
            "name" => "Siborong Borong",
            "city" => "Silangit",
            "provinsi" => "Sumatera Utara",
            "coordinates" => [2.2597, 98.9952], // Bandara Sisingamangaraja XII
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "SNB",
            "name" => "Sinabang",
            "city" => "Sinabang",
            "provinsi" => "Aceh",
            "coordinates" => [2.4166, 96.3291], // Bandara Lasikin
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "SKW",
            "name" => "Singkawang",
            "city" => "Singkawang",
            "provinsi" => "Kalimantan Barat",
            "coordinates" => [0.7909, 108.9578], // Bandara Singkawang
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "SIQ",
            "name" => "Singkep",
            "city" => "Singkep",
            "provinsi" => "Kepulauan Riau",
            "coordinates" => [-0.4816, 104.5811], // Bandara Dabo
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "SOC",
            "name" => "Solo",
            "city" => "Solo",
            "provinsi" => "Jawa Tengah",
            "coordinates" => [-7.5161, 110.7569], // Bandara Adi Soemarmo
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "SOQ",
            "name" => "Sorong",
            "city" => "Sorong",
            "provinsi" => "Papua Barat Daya",
            "coordinates" => [-0.8872, 131.2683], // Bandara Domine Eduard Osok
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "SWQ",
            "name" => "Sumbawa",
            "city" => "Sumbawa Besar",
            "provinsi" => "Nusa Tenggara Barat",
            "coordinates" => [-8.4890, 117.4120], // Bandara Sultan Muhammad Kaharuddin III
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "SUP",
            "name" => "Sumenep",
            "city" => "Sumenep",
            "provinsi" => "Jawa Timur",
            "coordinates" => [-7.0236, 113.8908], // Bandara Trunojoyo
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "SUB",
            "name" => "Surabaya",
            "city" => "Sidoarjo",
            "provinsi" => "Jawa Timur",
            "coordinates" => [-7.3797, 112.7869], // Bandara Juanda
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "TMC",
            "name" => "Tambolaka",
            "city" => "Tambolaka",
            "provinsi" => "Nusa Tenggara Timur",
            "coordinates" => [-9.4039, 119.2458], // Bandara Tambolaka
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "TMH",
            "name" => "Tanah Merah",
            "city" => "Tanah Merah",
            "provinsi" => "Papua Selatan",
            "coordinates" => [-6.1031, 140.3200], // Bandara Tanah Merah
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "TBK",
            "name" => "Tanjung Balai Karimun",
            "city" => "Tanjung Balai Karimun",
            "provinsi" => "Kepulauan Riau",
            "coordinates" => [1.0522, 103.3925], // Bandara Raja Haji Abdullah
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "TJS",
            "name" => "Tanjung Harapan",
            "city" => "Tanjung Selor",
            "provinsi" => "Kalimantan Utara",
            "coordinates" => [2.8363, 117.3736], // Bandara Tanjung Harapan
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "TJQ",
            "name" => "Tanjung Pandan",
            "city" => "Tanjung Pandan",
            "provinsi" => "Bangka Belitung",
            "coordinates" => [-2.7441, 107.7545], // Bandara H.A.S. Hanandjoeddin
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "TNJ",
            "name" => "Tanjung Pinang",
            "city" => "Tanjung Pinang",
            "provinsi" => "Kepulauan Riau",
            "coordinates" => [0.9183, 104.5266], // Bandara Raja Haji Fisabilillah
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "TRK",
            "name" => "Tarakan",
            "city" => "Tarakan",
            "provinsi" => "Kalimantan Utara",
            "coordinates" => [3.3266, 117.5655], // Bandara Juwata
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "TXM",
            "name" => "Teminabuan",
            "city" => "Teminabuan",
            "provinsi" => "Papua Barat Daya",
            "coordinates" => [-1.4394, 132.3081], // Bandara Teminabuan
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "TTE",
            "name" => "Ternate",
            "city" => "Ternate",
            "provinsi" => "Maluku Utara",
            "coordinates" => [0.8319, 127.3805], // Bandara Sultan Babullah
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "TIM",
            "name" => "Timika",
            "city" => "Timika",
            "provinsi" => "Papua Tengah",
            "coordinates" => [-4.5290, 136.8866], // Bandara Mozes Kilangin
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "LUV",
            "name" => "Tual, Karel Sadsuitu",
            "city" => "Tual",
            "provinsi" => "Maluku",
            "coordinates" => [-5.7602, 132.7594], // Bandara Karel Sadsuitubun
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "WGP",
            "name" => "Waingapu",
            "city" => "Waingapu",
            "provinsi" => "Nusa Tenggara Timur",
            "coordinates" => [-9.6691, 120.3019], // Bandara Umbu Mehang Kunda
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "WMX",
            "name" => "Wamena",
            "city" => "Wamena",
            "provinsi" => "Papua Pegunungan",
            "coordinates" => [-4.1008, 138.9567], // Bandara Wamena
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ],
          [
            "id" => "YIA",
            "name" => "Yogyakarta",
            "city" => "Kulon Progo",
            "provinsi" => "Daerah Istimewa Yogyakarta",
            "coordinates" => [-7.9075, 110.0544], // Bandara Internasional Yogyakarta
            "safetyReport" => ["rating" => "B", "lastInspection" => "2025-01-01", "notes" => "Data belum tersedia."]
          ]
        ];

        foreach ($airportData as $data) {
            Airport::create([ 
                'id' => $data['id'],
                'name' => $data['name'],
                'city' => $data['city'],
                'provinsi' => $data['provinsi'],
                'coordinates' => $data['coordinates'],
                'safetyReport' => $data['safetyReport'],
            ]);
        }
    }
}

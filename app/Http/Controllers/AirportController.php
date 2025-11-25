<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AirportController extends Controller
{
    /**
     * 1. API untuk Peta (Ringan)
     * Hanya mengembalikan data dasar untuk marker.
     */
    public function index()
    {
        // Ambil data bandara, tapi hitung total laporannya saja biar cepat
        $airports = Airport::withCount('reports')->get();
        
        // Format ulang agar sesuai dengan struktur frontend yang lama
        return $airports->map(function($airport) {
            return [
                'id' => $airport->id,
                'name' => $airport->name,
                'city' => $airport->city,
                'provinsi' => $airport->provinsi,
                'coordinates' => $airport->coordinates,
                'safetyReport' => $airport->safetyReport,
                'total_reports' => $airport->reports_count, // Hasil hitungan otomatis
            ];
        });
    }

    /**
     * 2. API untuk Sidebar (Detail & Berat)
     * Menghitung statistik bulanan dan kategori secara real-time.
     */
    public function stats(Request $request, $id)
    {
        $airport = Airport::findOrFail($id);
        
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // Query Dasar
        $query = $airport->reports();

        // Variabel untuk Trend
        $comparisonText = "Sepanjang Waktu";
        $growthPercentage = 0;
        $trendDirection = 'flat'; // up, down, flat
        $hasTrendData = false;

        // --- LOGIKA FILTER & TREND ---
        if ($startDate && $endDate) {
            // 1. Filter Periode Saat Ini
            $query->whereDate('report_date', '>=', $startDate)
                  ->whereDate('report_date', '<=', $endDate);

            // 2. Hitung Durasi Hari (misal 30 hari)
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            $daysDiff = $start->diffInDays($end) + 1;

            // 3. Tentukan Periode Sebelumnya (Mundur ke belakang sejumlah hari yang sama)
            $prevEnd = $start->copy()->subDay(); // Kemarin
            $prevStart = $prevEnd->copy()->subDays($daysDiff - 1); // Awal periode lalu

            // 4. Hitung Total Periode Lalu
            $prevTotal = $airport->reports()
                ->whereDate('report_date', '>=', $prevStart)
                ->whereDate('report_date', '<=', $prevEnd)
                ->count();

            // 5. Hitung Total Periode Ini (Akan dieksekusi di bawah)
            // Kita hitung nanti setelah query cloned.
            
            // Simpan info tanggal untuk frontend
            $comparisonText = "vs " . $prevStart->format('d M') . " - " . $prevEnd->format('d M Y');
            $hasTrendData = true;
        }

        // Clone query
        $qMonthly = clone $query;
        $qCategory = clone $query;
        $qStatus = clone $query;

        // Eksekusi Total Saat Ini
        $currentTotal = $query->count();

        // --- HITUNG PERSENTASE KENAIKAN/PENURUNAN ---
        if ($hasTrendData) {
            if ($prevTotal > 0) {
                $growth = (($currentTotal - $prevTotal) / $prevTotal) * 100;
                $growthPercentage = round(abs($growth), 1); // Ambil nilai positif
                $trendDirection = $growth > 0 ? 'up' : ($growth < 0 ? 'down' : 'flat');
            } else if ($currentTotal > 0) {
                $growthPercentage = 100; // Naik dari 0 ke ada
                $trendDirection = 'up';
            }
        }
        // --------------------------------------------

        // A. Bulanan
        $monthlyStats = $qMonthly
            ->select(DB::raw("DATE_FORMAT(report_date, '%Y-%m') as month_year"), DB::raw('count(*) as count'))
            ->groupBy('month_year')->orderBy('month_year', 'asc')->get()
            ->mapWithKeys(function ($item) {
                $date = Carbon::createFromFormat('Y-m', $item->month_year);
                return [$date->format('M Y') => $item->count];
            });

        // B. Kategori
        $categoryStats = $qCategory
            ->select('category', DB::raw('count(*) as count'))->whereNotNull('category')
            ->groupBy('category')->orderByDesc('count')->get()->pluck('count', 'category');

        // C. Status
        $statusStats = $qStatus
            ->select('status', DB::raw('count(*) as count'))->groupBy('status')->pluck('count', 'status');

        return response()->json([
            'airport_name' => $airport->name,
            'total_all_time' => $currentTotal,
            
            // --- DATA TREND BARU ---
            'trend_info' => [
                'has_data' => $hasTrendData,
                'percentage' => $growthPercentage,
                'direction' => $trendDirection, // 'up' atau 'down'
                'label' => $comparisonText
            ],
            // -----------------------

            'monthly_trend' => $monthlyStats,
            'top_categories' => $categoryStats,
            'status_summary' => [
                'open' => $statusStats['Analysis On Process'] ?? 0,
                'closed' => $statusStats['Analysis Completed'] ?? 0,
                'pending' => $statusStats['Send to Analyst'] ?? 0,
            ]
        ]);
    }

    /**
     * 3. API untuk Detail Laporan per Bulan
     * Request: /api/airports/JATSC/reports?month=2024-01
     */
    public function getReportsByMonth(Request $request, $id)
    {
        $month = $request->query('month'); // Format "YYYY-MM"

        if (!$month) {
            return response()->json(['error' => 'Month parameter required'], 400);
        }

        $reports = Airport::findOrFail($id)
            ->reports()
            ->whereRaw("DATE_FORMAT(report_date, '%Y-%m') = ?", [$month])
            ->orderBy('report_date', 'desc') // Urutkan dari tanggal terbaru
            ->get(['report_date', 'category', 'status', 'description']); // Ambil kolom penting saja

        return response()->json($reports);
    }
}
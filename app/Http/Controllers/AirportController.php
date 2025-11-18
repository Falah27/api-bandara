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
    public function stats($id)
    {
        $airport = Airport::findOrFail($id);

        // A. Hitung Tren Bulanan (Group by Year-Month)
        // Format output: ['Jan 2024' => 5, 'Feb 2024' => 12, ...]
        $monthlyStats = $airport->reports()
            ->select(
                DB::raw("DATE_FORMAT(report_date, '%Y-%m') as month_year"),
                DB::raw('count(*) as count')
            )
            ->groupBy('month_year')
            ->orderBy('month_year', 'asc')
            ->get()
            ->mapWithKeys(function ($item) {
                // Ubah '2024-01' menjadi 'Jan 24' agar enak dibaca
                $date = Carbon::createFromFormat('Y-m', $item->month_year);
                return [$date->format('M Y') => $item->count];
            });

        // B. Hitung Top Kategori (Group by Category)
        $categoryStats = $airport->reports()
            ->select('category', DB::raw('count(*) as count'))
            ->whereNotNull('category')
            ->groupBy('category')
            ->orderByDesc('count')
            ->get()
            ->pluck('count', 'category'); // Ubah jadi format ['Go Around' => 5, ...]

        // C. Hitung Status (Open/Closed)
        $statusStats = $airport->reports()
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        return response()->json([
            'airport_name' => $airport->name,
            'total_all_time' => $airport->reports()->count(),
            'monthly_trend' => $monthlyStats, // <--- INI YANG KITA CARI
            'top_categories' => $categoryStats,
            'status_summary' => [
                'open' => $statusStats['Analysis On Process'] ?? 0, // Sesuaikan string dengan CSV Anda
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
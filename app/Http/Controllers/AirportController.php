<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class AirportController extends Controller
{
    /**
     * Diagnosa data reports yang memiliki airport_id tidak valid.
     */
    public function checkDatabase()
    {
        $validIds = Airport::pluck('id')->toArray();

        $mismatches = Report::select('airport_id')
            ->distinct()
            ->whereNotIn('airport_id', $validIds)
            ->pluck('airport_id');

        return response()->json([
            'status' => 'Diagnostic Result',
            'message' => $mismatches->count() > 0 ? 'Mismatch Found' : 'Selamat! Semua data sinkron.',
            'total_mismatch' => $mismatches->count(),
            'data_mismatch' => $mismatches,
            'valid_ids_sample' => Airport::limit(5)->pluck('id')
        ]);
    }

    /**
     * Smart Query Logic
     */
    private function getSmartReportsQuery(Airport $airport): Builder
    {
        // 1. Cek Relasi Langsung
        $directQuery = $airport->reports();
        if ($directQuery->exists()) {
            return $directQuery->getQuery(); 
        }

        // 2. Cek by Name
        $queryByName = Report::where('airport_id', 'LIKE', "%{$airport->name}%");
        if ($queryByName->exists()) {
            return $queryByName;
        }

        // 3. Cek by Cleaned Name
        $cleanName = trim(str_replace(['Unit', 'Cabang Pembantu', 'Cabang', 'Pos', 'Bandara'], '', $airport->name));
        if (!empty($cleanName) && strlen($cleanName) >= 3) {
            $queryByCleanName = Report::where('airport_id', 'LIKE', "%{$cleanName}%");
            if ($queryByCleanName->exists()) {
                return $queryByCleanName;
            }
        }

        // 4. Cek by City
        if (!empty($airport->city) && strlen($airport->city) >= 3) {
            $queryByCity = Report::where('airport_id', 'LIKE', "%{$airport->city}%");
            if ($queryByCity->exists()) {
                return $queryByCity;
            }
        }

        return $airport->reports()->getQuery();
    }

    public function index()
    {
        $airports = Airport::get();

        return $airports->map(function ($airport) {
            $smartQuery = $this->getSmartReportsQuery($airport);
            
            return [
                'id' => $airport->id,
                'parent_id' => $airport->parent_id,
                'name' => $airport->name,
                'city' => $airport->city,
                'provinsi' => $airport->provinsi,
                'coordinates' => $airport->coordinates,
                'level' => $airport->level,
                'safetyReport' => $airport->safetyReport,
                'total_reports' => $smartQuery->count(),
            ];
        });
    }

    public function stats(Request $request, $id)
    {
        $airport = Airport::findOrFail($id);
        
        $validated = $request->validate([
            'start_date' => 'nullable|date|before_or_equal:today',
            'end_date'   => 'nullable|date|after_or_equal:start_date|before_or_equal:today',
        ]);

        $startDate = $validated['start_date'] ?? null;
        $endDate   = $validated['end_date'] ?? null;

        $query = $this->getSmartReportsQuery($airport);

        $allTimeQuery = clone $query;
        $totalAllTime = $allTimeQuery->count();

        $comparisonText = "Sepanjang Waktu";
        $growthPercentage = 0;
        $trendDirection = 'flat';
        $hasTrendData = false;
        $currentFilteredTotal = $totalAllTime;

        if ($startDate && $endDate) {
            $query->whereBetween('report_date', [$startDate, $endDate]);
            $currentFilteredTotal = $query->count();

            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            $daysDiff = $start->diffInDays($end) + 1;
            
            $prevEnd = $start->copy()->subDay();
            $prevStart = $prevEnd->copy()->subDays($daysDiff - 1);

            $prevQuery = $this->getSmartReportsQuery($airport);
            $prevTotal = $prevQuery->whereBetween('report_date', [$prevStart, $prevEnd])->count();

            $comparisonText = "vs " . $prevStart->format('d M') . " - " . $prevEnd->format('d M Y');
            $hasTrendData = true;

            if ($prevTotal > 0) {
                $growth = (($currentFilteredTotal - $prevTotal) / $prevTotal) * 100;
                $growthPercentage = round(abs($growth), 1);
                $trendDirection = $growth > 0 ? 'up' : ($growth < 0 ? 'down' : 'flat');
            } else if ($currentFilteredTotal > 0) {
                $growthPercentage = 100;
                $trendDirection = 'up';
            }
        }

        $qMonthly  = clone $query;
        $qCategory = clone $query;
        $qStatus   = clone $query;

        $monthlyStats = $qMonthly
            ->select(DB::raw("DATE_FORMAT(report_date, '%Y-%m') as month_year"), DB::raw('count(*) as count'))
            ->groupBy('month_year')
            ->orderBy('month_year', 'asc')
            ->get()
            ->mapWithKeys(function ($item) {
                $date = Carbon::createFromFormat('Y-m', $item->month_year);
                return [$date->format('M Y') => $item->count];
            });

        $categoryStats = $qCategory
            ->select('category', DB::raw('count(*) as count'))
            ->whereNotNull('category')
            ->groupBy('category')
            ->orderByDesc('count')
            ->pluck('count', 'category');

        $statusStats = $qStatus
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        return response()->json([
            'airport_name'   => $airport->name,
            'total_display'  => $currentFilteredTotal,
            'total_all_time' => $totalAllTime,
            'trend_info' => [
                'has_data'   => $hasTrendData,
                'percentage' => $growthPercentage,
                'direction'  => $trendDirection,
                'label'      => $comparisonText
            ],
            'monthly_trend'  => $monthlyStats,
            'top_categories' => $categoryStats,
            'status_summary' => [
                'open'    => $statusStats['Analysis On Process'] ?? 0,
                'closed'  => $statusStats['Analysis Completed'] ?? 0,
                'pending' => $statusStats['Send to Analyst'] ?? 0,
            ]
        ]);
    }

    // Untuk LIST: Mengambil data ringkas saja agar Card Simple & Cepat
    public function getReports(Request $request, $id)
    {
        $airport = Airport::findOrFail($id);
        $query = $this->getSmartReportsQuery($airport);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('report_date', [$request->start_date, $request->end_date]);
        }
        
        if ($request->filled('month')) {
            try {
                $date = Carbon::createFromFormat('Y-m', $request->month);
                $query->whereYear('report_date', $date->year)
                      ->whereMonth('report_date', $date->month);
            } catch (\Exception $e) {}
        }

        // OPTIMASI: Hanya ambil kolom penting untuk Card Simple
        // Data detail (description, evidence/foto, dll) diambil nanti saat klik (via detailReport)
        // Ini membuat response JSON jauh lebih kecil dan cepat.
        $reports = $query->orderBy('report_date', 'desc')
            ->select(['id', 'report_date', 'category', 'status']) 
            ->get();

        return response()->json($reports);
    }

    // ✅ FUNGSI BARU: Mengambil Detail Lengkap Laporan (Dipanggil saat Card diklik)
    public function detailReport($id)
    {
        $report = Report::findOrFail($id);
        
        // Kembalikan semua data (termasuk description, location, evidence, dll)
        return response()->json($report);
    }

    public function hierarchy($id)
    {
        $airport = Airport::with('children')->findOrFail($id);
        $children = $airport->children;

        $formatChild = function($item) {
            $smartQuery = $this->getSmartReportsQuery($item);
            $count = $smartQuery->count();
            
            return [
                'id' => $item->id,
                'name' => $item->name,
                'city' => $item->city,
                'provinsi' => $item->provinsi,
                'level' => $item->level,
                'reports_count' => $count,
                'has_reports' => $count > 0
            ];
        };

        return response()->json([
            'cabang_pembantu' => $children->where('level', 'cabang_pembantu')->values()->map($formatChild),
            'units' => $children->where('level', 'unit')->values()->map($formatChild),
            'total_children' => $children->count()
        ]);
    }
}
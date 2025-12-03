<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AirportController extends Controller
{
    /**
     * 1. API untuk Peta (Dengan Hierarki)
     * Return Cabang beserta Unit-unit di bawahnya
     */
    public function index(Request $request)
    {
        $type = $request->query('type');
        $parentId = $request->query('parent_id');

        // Gunakan 'withCount' agar frontend tahu mana marker yang punya laporan
        $query = Airport::withCount('reports');

        // LOGIKA PERBAIKAN:
        if ($parentId) {
            // KASUS 1: Jika user minta Anak (misal ?parent_id=MATSC)
            $query->where('parent_id', $parentId);
            
            if ($type) {
                $query->where('type', $type);
            }
            
        } else {
            // KASUS 2: Initial Load - Tampilkan Cabang saja
            if ($type) {
                $query->where('type', $type);
            } else {
                $query->where('type', 'cabang');
            }
        }

        $airports = $query->get();

        // ✅ PERBAIKAN BARU: Mapping dengan Sub-Units
        return $airports->map(function($airport) {
            $data = [
                'id' => $airport->id,
                'name' => $airport->name,
                'city' => $airport->city,
                'provinsi' => $airport->provinsi,
                'coordinates' => $airport->coordinates,
                'safetyReport' => $airport->safetyReport,
                'total_reports' => $airport->reports_count,
                'type' => $airport->type,
                'parent_id' => $airport->parent_id,
            ];

            // ✅ TAMBAHKAN SUB_UNITS JIKA INI CABANG
            if ($airport->type === 'cabang') {
                // Ambil semua anak-anaknya (unit/cabang_pembantu)
                $data['sub_units'] = $airport->children->map(function($child) {
                    return [
                        'id' => $child->id,
                        'name' => $child->name,
                        'coordinates' => $child->coordinates,
                        'type' => $child->type,
                        'service' => $child->service_level ?? 'N/A',
                    ];
                });
            }

            return $data;
        });
    }

    public function stats(Request $request, $id)
    {
        $airport = Airport::findOrFail($id);
        $validated = $request->validate([
            'start_date' => 'nullable|date|before_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date|before_or_equal:today',
        ]);

        $startDate = $validated['start_date'] ?? null;
        $endDate = $validated['end_date'] ?? null;

        // Query Dasar
        $query = $airport->reports();

        // Variabel untuk Trend
        $comparisonText = "Sepanjang Waktu";
        $growthPercentage = 0;
        $trendDirection = 'flat';
        $hasTrendData = false;

        // --- LOGIKA FILTER & TREND ---
        if ($startDate && $endDate) {
            $query->whereDate('report_date', '>=', $startDate)
                  ->whereDate('report_date', '<=', $endDate);

            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            $daysDiff = $start->diffInDays($end) + 1;

            $prevEnd = $start->copy()->subDay();
            $prevStart = $prevEnd->copy()->subDays($daysDiff - 1);

            $prevTotal = $airport->reports()
                ->whereDate('report_date', '>=', $prevStart)
                ->whereDate('report_date', '<=', $prevEnd)
                ->count();

            $comparisonText = "vs " . $prevStart->format('d M') . " - " . $prevEnd->format('d M Y');
            $hasTrendData = true;
        }

        // Clone query
        $qMonthly = clone $query;
        $qCategory = clone $query;
        $qStatus = clone $query;

        $currentTotal = $query->count();

        // --- HITUNG PERSENTASE ---
        if ($hasTrendData) {
            if ($prevTotal > 0) {
                $growth = (($currentTotal - $prevTotal) / $prevTotal) * 100;
                $growthPercentage = round(abs($growth), 1);
                $trendDirection = $growth > 0 ? 'up' : ($growth < 0 ? 'down' : 'flat');
            } else if ($currentTotal > 0) {
                $growthPercentage = 100;
                $trendDirection = 'up';
            }
        }

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
            
            'trend_info' => [
                'has_data' => $hasTrendData,
                'percentage' => $growthPercentage,
                'direction' => $trendDirection,
                'label' => $comparisonText
            ],

            'monthly_trend' => $monthlyStats,
            'top_categories' => $categoryStats,
            'status_summary' => [
                'open' => $statusStats['Analysis On Process'] ?? 0,
                'closed' => $statusStats['Analysis Completed'] ?? 0,
                'pending' => $statusStats['Send to Analyst'] ?? 0,
            ]
        ]);
    }

    public function getReportsByMonth(Request $request, $id)
    {
        $validated = $request->validate([
            'month' => 'required|date_format:Y-m'
        ]);

        $month = $validated['month'];

        $reports = Airport::findOrFail($id)
            ->reports()
            ->whereRaw("DATE_FORMAT(report_date, '%Y-%m') = ?", [$month])
            ->orderBy('report_date', 'desc')
            ->get(['report_date', 'category', 'status', 'description']);

        return response()->json($reports);
    }
}
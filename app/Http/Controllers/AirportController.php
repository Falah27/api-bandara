<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AirportController extends Controller
{
    /**
     * 1. API DATA UTAMA (Untuk Peta) - Optimized
     */
    public function index()
    {
        // Ambil semua airport
        $airports = Airport::withCount('reports')->get();
        
        return $airports->map(function($airport) {
            return [
                'id' => $airport->id,
                'parent_id' => $airport->parent_id, // <--- TAMBAHKAN INI
                'name' => $airport->name,
                'city' => $airport->city,
                'provinsi' => $airport->provinsi,
                'coordinates' => $airport->coordinates,
                'level' => $airport->level, 
                'safetyReport' => $airport->safetyReport,
                'total_reports' => $airport->reports_count,
            ];
        });
    }

    /**
     * 2. API STATISTIK (Chart & Summary) - Optimized
     */
    public function stats(Request $request, $id)
    {
        $airport = Airport::select('id', 'name')->findOrFail($id);
        $validated = $request->validate([
            'start_date' => 'nullable|date|before_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date|before_or_equal:today',
        ]);

        $startDate = $validated['start_date'] ?? null;
        $endDate = $validated['end_date'] ?? null;

        $query = $airport->reports();

        // Filter Tanggal
        if ($startDate && $endDate) {
            $query->whereDate('report_date', '>=', $startDate)
                  ->whereDate('report_date', '<=', $endDate);
        }

        // Build base query with only needed columns
        $baseQuery = $query->select('report_date', 'category');
        
        $currentTotal = (clone $baseQuery)->count();
        
        // Data Bulanan
        $monthlyStats = (clone $baseQuery)
            ->select(DB::raw("DATE_FORMAT(report_date, '%Y-%m') as month_year"), DB::raw('count(*) as count'))
            ->groupBy('month_year')->orderBy('month_year', 'asc')->get()
            ->mapWithKeys(function ($item) {
                $date = Carbon::createFromFormat('Y-m', $item->month_year);
                return [$date->format('M Y') => $item->count];
            });

        // Data Kategori
        $categoryStats = (clone $baseQuery)
            ->whereNotNull('category')
            ->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')->orderByDesc('count')->get()->pluck('count', 'category');

        return response()->json([
            'airport_name' => $airport->name,
            'total_all_time' => $currentTotal,
            'monthly_trend' => $monthlyStats,
            'top_categories' => $categoryStats,
        ]);
    }

    /**
     * 3. API HIERARKI (Struktur Organisasi) - Optimized
     */
    public function hierarchy($id)
    {
        $airport = Airport::with(['children' => function($q) {
            $q->withCount('reports');
        }])->findOrFail($id);

        $children = $airport->children;

        // Helper untuk format data anak
        $formatChild = function($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'city' => $item->city,         // <--- TAMBAHAN: Agar Header Sidebar bisa update
                'provinsi' => $item->provinsi, // <--- TAMBAHAN
                'level' => $item->level,       // <--- TAMBAHAN
                'reports_count' => $item->reports_count,
                'has_reports' => $item->reports_count > 0
            ];
        };

        return response()->json([
            'cabang_pembantu' => $children->where('level', 'cabang_pembantu')->values()->map($formatChild),
            'units' => $children->where('level', 'unit')->values()->map($formatChild),
            'total_children' => $children->count()
        ]);
    }

    /**
     * 4. API DETAIL LAPORAN BULANAN
     */
    public function getReportsByMonth(Request $request, $id)
    {
        $validated = $request->validate(['month' => 'required|date_format:Y-m']);
        $month = $validated['month'];

        $reports = Airport::findOrFail($id)
            ->reports()
            ->whereRaw("DATE_FORMAT(report_date, '%Y-%m') = ?", [$month])
            ->orderBy('report_date', 'desc')
            ->get(['report_date', 'category', 'status', 'description']);

        return response()->json($reports);
    }
}
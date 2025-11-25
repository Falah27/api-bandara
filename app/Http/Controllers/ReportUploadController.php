<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ReportsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Report;
use Carbon\Carbon; 

class ReportUploadController extends Controller
{
    public function upload(Request $request) 
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls'
        ]);

        try {
            $importer = new ReportsImport;
            Excel::import($importer, $request->file('file'));
            
            $count = $importer->rowCount;
            
            $dateRangeMessage = "";
            if ($count > 0 && $importer->minDate && $importer->maxDate) {
                $start = $importer->minDate->format('d M Y');
                $end = $importer->maxDate->format('d M Y');
                $dateRangeMessage = " dari tanggal {$start} s/d {$end}";
            }

            $message = "Berhasil memproses {$count} data{$dateRangeMessage}.";
            
            return response()->json(['message' => $message], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function deleteRange(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        try {
            // 1. BERSIHKAN FORMAT TANGGAL (Menggunakan Carbon)
            // ->format('Y-m-d') mengubahnya jadi format MySQL (misal: 2025-11-01)
            $cleanStartDate = Carbon::parse($request->start_date)->format('Y-m-d');
            $cleanEndDate   = Carbon::parse($request->end_date)->format('Y-m-d');

            // 2. JALANKAN HAPUS
            $deletedCount = Report::whereDate('report_date', '>=', $cleanStartDate)
                                  ->whereDate('report_date', '<=', $cleanEndDate)
                                  ->delete();

            // Format tanggal untuk pesan balik ke user
            $startStr = Carbon::parse($cleanStartDate)->format('d M Y');
            $endStr   = Carbon::parse($cleanEndDate)->format('d M Y');

            return response()->json([
                'message' => "Anda berhasil hapus {$deletedCount} data dari tanggal {$startStr} s/d {$endStr}.",
                'count' => $deletedCount
            ], 200);

        } catch (\Exception $e) {
            // Ini akan menangkap error jika Carbon gagal atau DB error
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
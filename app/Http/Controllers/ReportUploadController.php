<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ReportsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Report; 

class ReportUploadController extends Controller
{
    public function upload(Request $request) 
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls'
        ]);

        try {
            Excel::import(new ReportsImport, $request->file('file'));
            return response()->json(['message' => 'Data berhasil diupdate!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ... fungsi upload yang lama ...

    // ... fungsi upload yang lama ...

    public function deleteRange(Request $request)
    {

        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        try {
            $deletedCount = Report::whereDate('report_date', '>=', $request->start_date)
                                  ->whereDate('report_date', '<=', $request->end_date)
                                  ->delete();

            return response()->json([
                'message' => "Berhasil menghapus {$deletedCount} laporan.",
                'count' => $deletedCount
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
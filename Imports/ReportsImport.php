<?php

namespace App\Imports;

use App\Models\Report;
use App\Models\Airport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;

class ReportsImport implements ToModel, WithStartRow
{
    private $airports;

    public function __construct()
    {
        // Cache ID bandara biar cepat
        $this->airports = Airport::all()->pluck('id', 'name')->mapWithKeys(function ($item, $key) {
            return [strtoupper($key) => $item];
        })->toArray();
    }

    // Mulai baca dari baris ke-5 (melewati header)
    public function startRow(): int
    {
        return 5;
    }

    public function model(array $row)
    {
        // Validasi: Pastikan ada nama cabang (Kolom E / index 4)
        if (!isset($row[4])) return null;

        $branchName = strtoupper(trim($row[4]));
        
        // Cek apakah bandara dikenali
        if (!isset($this->airports[$branchName])) {
            return null; 
        }

        // Parsing Tanggal (Kolom C / index 2)
        $dateString = $row[2];
        $reportDate = null;
        try {
            if (is_numeric($dateString)) {
                // Format Excel numeric
                $reportDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateString);
            } else {
                // Format string 'dd/mm/yyyy HH:mm'
                $reportDate = Carbon::createFromFormat('d/m/Y H:i', $dateString);
            }
        } catch (\Exception $e) {
            try {
                $reportDate = Carbon::parse($dateString);
            } catch (\Exception $x) {
                return null; // Tanggal rusak, skip
            }
        }

        // --- LOGIKA ANTI DUPLIKAT ---
        // Cek apakah data sudah ada berdasarkan Bandara, Tanggal, dan Kategori
        return Report::firstOrCreate(
            [
                'airport_id'  => $this->airports[$branchName],
                'report_date' => $reportDate,
                'category'    => $row[8] ?? null, // Kolom I / index 8
            ],
            [
                'description' => $row[38] ?? null, // Kolom AM / index 38
                'status'      => $row[40] ?? null, // Kolom AO / index 40
            ]
        );
    }
}
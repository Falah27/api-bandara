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
        // Cache ID bandara (Nama Uppercase -> ID)
        $this->airports = Airport::all()->pluck('id', 'name')->mapWithKeys(function ($item, $key) {
            return [strtoupper($key) => $item];
        })->toArray();
    }

    // Data mulai dari baris ke-5 (karena ada header di atasnya)
    public function startRow(): int
    {
        return 5;
    }

    public function model(array $row)
    {
        // Kolom 4 (E) = Branch/Lokasi
        if (!isset($row[4])) return null;
        $branchName = strtoupper(trim($row[4]));

        // Cek apakah bandara dikenali
        if (!isset($this->airports[$branchName])) {
            return null; 
        }

        // Kolom 2 (C) = Tanggal
        // Logika parsing tanggal yang kuat (bisa baca Excel Serial atau String)
        $dateString = $row[2];
        $reportDate = null;
        try {
            if (is_numeric($dateString)) {
                $reportDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateString);
            } else {
                // Format d/m/Y H:i (Indo)
                $reportDate = Carbon::createFromFormat('d/m/Y H:i', $dateString);
            }
        } catch (\Exception $e) {
            try {
                $reportDate = Carbon::parse($dateString);
            } catch (\Exception $x) {
                return null; // Skip jika tanggal rusak
            }
        }

        // --- LOGIKA ANTI DUPLIKAT ---
        // Cari data yang SAMA PERSIS (Bandara + Tanggal + Kategori).
        // Jika ada -> Pakai yang lama. Jika tidak -> Buat baru.
        return Report::updateOrCreate(
            [
                // [KUNCI UNIK]
                // Sistem akan mengecek: "Apakah ada laporan di Bandara ini, Tanggal segini, Kategori ini?"
                'airport_id'  => $this->airports[$branchName],
                'report_date' => $reportDate,
                'category'    => $row[8] ?? null, 
            ],
            [
                // [DATA YANG DI-UPDATE]
                // Jika data sudah ada, kolom ini akan ditimpa (berguna jika ada revisi deskripsi/status)
                'description' => $row[38] ?? null,
                'status'      => $row[40] ?? null,
            ]
        );
    }
}
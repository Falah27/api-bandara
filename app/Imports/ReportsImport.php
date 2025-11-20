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

    public $rowCount = 0;
    public $minDate = null;
    public $maxDate = null;

    public function __construct()
    {
        $this->airports = Airport::all()->pluck('id', 'name')->mapWithKeys(function ($item, $key) {
            return [strtoupper($key) => $item];
        })->toArray();
    }

    public function startRow(): int
    {
        return 5;
    }

    public function model(array $row)
    {
        if (!isset($row[4])) return null;
        $branchName = strtoupper(trim($row[4]));
        
        if (!isset($this->airports[$branchName])) return null; 

        $dateString = $row[2];
        $reportDate = null;

        // --- PERBAIKAN UTAMA: MEMAKSA JADI CARBON ---
        try {
            if (is_numeric($dateString)) {
                // Jika format Excel numeric, ubah ke DateTime dulu, LALU ke Carbon
                $dateTime = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateString);
                $reportDate = Carbon::instance($dateTime); 
            } else {
                // Format string
                $reportDate = Carbon::createFromFormat('d/m/Y H:i', $dateString);
            }
        } catch (\Exception $e) {
            try {
                // Fallback parsing
                $reportDate = Carbon::parse($dateString);
            } catch (\Exception $x) {
                return null; 
            }
        }
        // -------------------------------------------

        if ($reportDate) {
            $this->rowCount++;

            // Sekarang aman menggunakan lt() dan gt() karena pasti Carbon
            if ($this->minDate === null || $reportDate->lt($this->minDate)) {
                $this->minDate = $reportDate;
            }

            if ($this->maxDate === null || $reportDate->gt($this->maxDate)) {
                $this->maxDate = $reportDate;
            }
        }

        return Report::updateOrCreate(
            [
                'airport_id'  => $this->airports[$branchName],
                'report_date' => $reportDate,
                'category'    => $row[8] ?? null,
            ],
            [
                'description' => $row[38] ?? null,
                'status'      => $row[40] ?? null,
            ]
        );
    }
}
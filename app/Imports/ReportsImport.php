<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class ReportsImport implements ToModel, WithChunkReading, WithBatchInserts
{
    public function model(array $row)
    {
        // Kita tidak menggunakan logika model otomatis disini 
        // karena kita memproses array manual di controller.
        return null;
    }

    /**
     * Baca file dalam chunk untuk menghindari memory overflow
     */
    public function chunkSize(): int
    {
        return 500; // Proses 500 baris sekaligus
    }

    /**
     * Insert dalam batch untuk performa lebih baik
     */
    public function batchSize(): int
    {
        return 100; // Insert 100 row per query
    }
}
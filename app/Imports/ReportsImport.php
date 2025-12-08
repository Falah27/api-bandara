<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;

class ReportsImport implements ToModel
{
    public function model(array $row)
    {
        // Kita tidak menggunakan logika model otomatis disini 
        // karena kita memproses array manual di controller.
        return null;
    }
}
<?php

namespace Database\Seeders;

use App\Models\Airport;
use App\Models\Report;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(AirportSeeder::class);
        $this->call(ReportTotalSeeder::class);
        $this->call(ReportCategorySeeder::class);
        $this->call(RawReportSeeder::class);
    }
}


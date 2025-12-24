<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompleteExcelColumnsToReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            // Kolom yang masih kurang dari Excel
            $table->string('type_f')->nullable()->after('flight_type'); // Type F (Kolom T)
            $table->string('dta')->nullable()->after('type_f'); // DTA (Kolom U)
            $table->string('itp')->nullable()->after('dta'); // ITP (Kolom V)
            $table->text('add_info')->nullable()->after('itp'); // Additional Info (Kolom W)
            $table->string('flight')->nullable()->after('add_info'); // Flight (Kolom Y)
            
            // Koordinat & Posisi
            $table->decimal('latitude', 10, 7)->nullable()->after('weather_condition'); // Lat (Kolom Z)
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude'); // Long (Kolom AA)
            $table->string('altitude')->nullable()->after('longitude'); // Alt (Kolom AB)
            $table->string('horizontal_distance')->nullable()->after('altitude'); // Horizontal Dist (Kolom AC)
            $table->string('vertical_distance')->nullable()->after('horizontal_distance'); // Vertical Dist (Kolom AD)
            $table->string('time_qam')->nullable()->after('vertical_distance'); // Time QAM (Kolom AE)
            
            // Weather tambahan
            $table->string('pressure_wx')->nullable()->after('visibility'); // Pressure WX (Kolom AH)
            $table->string('altimeter')->nullable()->after('temperature'); // Altimeter (Kolom AK)
            
            // Status Analyst (sudah ada di kolom status_investigasi, tapi buat kolom terpisah)
            $table->string('status_analyst')->nullable()->after('status_investigasi'); // Status Analyst (Kolom AM)
            
            // Index untuk kolom yang sering diquery
            $table->index('latitude');
            $table->index('longitude');
            $table->index('status_analyst');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex(['latitude']);
            $table->dropIndex(['longitude']);
            $table->dropIndex(['status_analyst']);
            
            $table->dropColumn([
                'type_f', 'dta', 'itp', 'add_info', 'flight',
                'latitude', 'longitude', 'altitude', 
                'horizontal_distance', 'vertical_distance', 'time_qam',
                'pressure_wx', 'altimeter', 'status_analyst'
            ]);
        });
    }
}

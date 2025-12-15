<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Menambahkan index untuk performa query
     */
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Index untuk query filter by airport
            $table->index('airport_id', 'idx_reports_airport_id');
            
            // Index untuk query filter by date range
            $table->index('report_date', 'idx_reports_date');
            
            // Index untuk query filter by category
            $table->index('category', 'idx_reports_category');
            
            // Index untuk query filter by status
            $table->index('status', 'idx_reports_status');
            
            // Composite index untuk query kombinasi (airport + date)
            $table->index(['airport_id', 'report_date'], 'idx_reports_airport_date');
        });

        Schema::table('airports', function (Blueprint $table) {
            // Index untuk hierarchy query
            $table->index('parent_id', 'idx_airports_parent_id');
            
            // Index untuk level filtering
            $table->index('level', 'idx_airports_level');
            
            // Index untuk city lookup
            $table->index('city', 'idx_airports_city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex('idx_reports_airport_id');
            $table->dropIndex('idx_reports_date');
            $table->dropIndex('idx_reports_category');
            $table->dropIndex('idx_reports_status');
            $table->dropIndex('idx_reports_airport_date');
        });

        Schema::table('airports', function (Blueprint $table) {
            $table->dropIndex('idx_airports_parent_id');
            $table->dropIndex('idx_airports_level');
            $table->dropIndex('idx_airports_city');
        });
    }
};

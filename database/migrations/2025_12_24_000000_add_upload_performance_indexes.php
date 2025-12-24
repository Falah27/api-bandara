<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Index untuk mempercepat pengecekan duplikat saat upload
     */
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Composite index untuk pengecekan duplikat yang cepat
            // Kombinasi: airport_id + report_date + description (partial)
            $table->index(['airport_id', 'report_date'], 'idx_airport_date');
            
            // Index untuk query berdasarkan tanggal (untuk delete range, dll)
            $table->index('report_date', 'idx_report_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex('idx_airport_date');
            $table->dropIndex('idx_report_date');
        });
    }
};

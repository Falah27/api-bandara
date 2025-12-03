<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->index(['airport_id', 'report_date'], 'idx_airport_date');
            $table->index('category', 'idx_category');
            $table->index('status', 'idx_status');
            $table->index('report_date', 'idx_report_date');
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
            // Hapus index kalau rollback
            $table->dropIndex('idx_airport_date');
            $table->dropIndex('idx_category');
            $table->dropIndex('idx_status');
            $table->dropIndex('idx_report_date');
        });
    }
}
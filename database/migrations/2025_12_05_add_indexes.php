<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Add indexes for frequently queried columns
            $table->index('airport_id');
            $table->index('report_date');
            $table->index('category');
            // Composite index for filtering by airport and date
            $table->index(['airport_id', 'report_date']);
        });

        Schema::table('airports', function (Blueprint $table) {
            // Add indexes for hierarchy queries
            $table->index('parent_id');
            $table->index('level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex(['airport_id']);
            $table->dropIndex(['report_date']);
            $table->dropIndex(['category']);
            $table->dropIndex(['airport_id', 'report_date']);
        });

        Schema::table('airports', function (Blueprint $table) {
            $table->dropIndex(['parent_id']);
            $table->dropIndex(['level']);
        });
    }
};

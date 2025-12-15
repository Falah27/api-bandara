<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Mapping table untuk optimize smart query
     */
    public function up(): void
    {
        // Tambah soft deletes ke reports
        Schema::table('reports', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Buat mapping table untuk optimize lookup
        Schema::create('airport_name_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('excel_name')->index(); // Nama dari Excel
            $table->string('airport_id')->index(); // ID Airport yang sesuai
            $table->integer('match_count')->default(1); // Berapa kali mapping ini digunakan
            $table->enum('match_type', ['exact', 'city', 'name_like', 'manual'])->default('exact');
            $table->timestamps();

            // Unique constraint: 1 excel_name = 1 airport_id
            $table->unique(['excel_name', 'airport_id']);

            // Foreign key
            $table->foreign('airport_id')->references('id')->on('airports')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::dropIfExists('airport_name_mappings');
    }
};

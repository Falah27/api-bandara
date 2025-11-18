<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id(); // ID unik 1, 2, 3...
            $table->string('airport_id'); // Penghubung ke tabel 'airports'
            
            // Kolom dari CSV mentah Anda
            $table->timestamp('report_date')->nullable(); // Dari kolom 'Date'
            $table->string('category')->nullable(); // Dari kolom 'Category'
            $table->string('status')->nullable(); // Dari kolom 'Status Analyst'
            $table->text('description')->nullable(); // Dari kolom 'Des'
            
            // Membuat 'constraint' (hubungan)
            $table->foreign('airport_id')
                  ->references('id')
                  ->on('airports')
                  ->onDelete('cascade');
                  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reports');
    }
}

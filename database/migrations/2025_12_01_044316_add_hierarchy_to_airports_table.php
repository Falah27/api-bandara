<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHierarchyToAirportsTable extends Migration
{
    public function up()
    {
        Schema::table('airports', function (Blueprint $table) {
            // Menambahkan kolom type
            $table->enum('type', ['cabang', 'cabang_pembantu', 'unit'])
                  ->default('unit')
                  ->after('id');
            $table->string('parent_id')->nullable()->after('type');
            $table->text('service_level')->nullable()->after('parent_id');
            $table->foreign('parent_id')
                  ->references('id')
                  ->on('airports')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('airports', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['type', 'parent_id']);
        });
    }
}
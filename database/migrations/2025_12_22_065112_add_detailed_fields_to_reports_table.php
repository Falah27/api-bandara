<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailedFieldsToReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            // Identifikasi & Tracking
            $table->string('effort_id')->nullable()->after('id');
            $table->dateTime('input_date')->nullable()->after('report_date');
            
            // Lokasi Detail
            $table->string('location')->nullable()->after('description'); // Lokasi spesifik
            $table->string('ats_unit')->nullable()->after('location'); // TWR/APP/ACC
            $table->string('classification')->nullable()->after('category'); // Incident/Hazard/Accident
            
            // Informasi Pesawat
            $table->string('ssr_code')->nullable()->after('classification');
            $table->string('aircraft_id')->nullable(); // Call sign
            $table->string('aircraft_reg')->nullable(); // Registrasi
            $table->string('aircraft_type')->nullable(); // B738/A320
            $table->string('pic_name')->nullable(); // Pilot in Command
            $table->string('operator')->nullable(); // Maskapai
            
            // Flight Information
            $table->string('flight_rules')->nullable(); // IFR/VFR
            $table->string('flight_phase')->nullable(); // Approach/Cruise/Landing
            $table->string('departure_airport')->nullable(); // ADEP
            $table->string('destination_airport')->nullable(); // ADES
            $table->string('flight_type')->nullable(); // Scheduled/Non-scheduled
            
            // Weather & Environment
            $table->string('weather_condition')->nullable(); // VMC/IMC
            $table->string('wind')->nullable();
            $table->string('visibility')->nullable();
            $table->string('cloud')->nullable();
            $table->string('temperature')->nullable();
            
            // Additional Info
            $table->text('remark')->nullable();
            $table->string('status_investigasi')->nullable();
            
            // Index untuk performa query
            $table->index('effort_id');
            $table->index('aircraft_id');
            $table->index('operator');
            $table->index('flight_phase');
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
            $table->dropIndex(['effort_id']);
            $table->dropIndex(['aircraft_id']);
            $table->dropIndex(['operator']);
            $table->dropIndex(['flight_phase']);
            
            $table->dropColumn([
                'effort_id', 'input_date', 'location', 'ats_unit', 'classification',
                'ssr_code', 'aircraft_id', 'aircraft_reg', 'aircraft_type',
                'pic_name', 'operator', 'flight_rules', 'flight_phase',
                'departure_airport', 'destination_airport', 'flight_type',
                'weather_condition', 'wind', 'visibility', 'cloud', 
                'temperature', 'remark', 'status_investigasi'
            ]);
        });
    }
}

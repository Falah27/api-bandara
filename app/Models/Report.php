<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use HasFactory, SoftDeletes;

    // Kolom yang diisi oleh RawReportSeeder
    protected $fillable = [
        'id',
        'report_date',
        'input_date',
        'category',
        'classification',
        'ssr_code',
        'status',
        'description',
        'airport_id',
        'location',
<<<<<<< HEAD
=======
        'ats_unit',
        'effort_id',
        // Detail aircraft & flight
        'aircraft_id',
        'aircraft_reg',
        'aircraft_type',
        'pic_name',
        'operator',
        'flight_rules',
        'flight_phase',
        'departure_airport',
        'destination_airport',
        'flight_type',
        'type_f',
        'dta',
        'itp',
        'add_info',
        'flight',
        // Koordinat & Posisi
        'latitude',
        'longitude',
        'altitude',
        'horizontal_distance',
        'vertical_distance',
        'time_qam',
        // Weather
        'weather_condition',
        'wind',
        'visibility',
        'pressure_wx',
        'cloud',
        'temperature',
        'altimeter',
        'remark',
        'status_investigasi',
        'status_analyst',
>>>>>>> 754487c (24/12)
    ];

    // Otomatis ubah tanggal
    protected $casts = [
        'report_date' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * SATU Laporan (Report) DIMILIKI OLEH SATU Airport
     */
    public function airport()
    {
        return $this->belongsTo(Airport::class, 'airport_id', 'id');
    }
}
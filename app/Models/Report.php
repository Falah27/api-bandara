<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    // Kolom yang diisi oleh RawReportSeeder
    protected $fillable = [
        'id',
        'report_date',
        'category',
        'status',
        'description',
        'airport_id',
    ];

    // Otomatis ubah tanggal
    protected $casts = [
        'report_date' => 'datetime',
    ];

    /**
     * SATU Laporan (Report) DIMILIKI OLEH SATU Airport
     */
    public function airport()
    {
        return $this->belongsTo(Airport::class, 'airport_id', 'id');
    }
}
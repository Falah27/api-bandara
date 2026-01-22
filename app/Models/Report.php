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
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Airport extends Model
{
    use HasFactory;
    protected $fillable = [
        'id', 'name', 'city', 'provinsi', 'coordinates', 'safetyReport', 'total_reports', 'report_categories'
    ];
    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'coordinates' => 'array',
        'safetyReport' => 'array',
        'report_categories' => 'array', 
    ];

    public function reports()
    {
        return $this->hasMany(Report::class, 'airport_id', 'id');
    }
}
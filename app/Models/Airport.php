<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Airport extends Model
{
    use HasFactory;
    protected $fillable = [
        'id', 
        'name', 
        'city', 
        'provinsi', 
        'coordinates', 
        'safetyReport',
        // 'type', 
        // 'parent_id', 
        // 'service_level'
    ];

    public function children()
    {
        return $this->hasMany(Airport::class, 'parent_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(Airport::class, 'parent_id', 'id');
    }
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
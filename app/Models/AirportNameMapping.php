<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirportNameMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'excel_name',
        'airport_id',
        'match_count',
        'match_type',
    ];

    /**
     * Relasi ke Airport
     */
    public function airport()
    {
        return $this->belongsTo(Airport::class, 'airport_id', 'id');
    }

    /**
     * Increment match count (tracking usage)
     */
    public function incrementUsage()
    {
        $this->increment('match_count');
    }
}

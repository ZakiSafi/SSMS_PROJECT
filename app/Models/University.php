<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class University extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'teachers',
        'type',
        'province_id',
    ];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function faculties()
    {
        return $this->hasMany(Faculty::class);
    }

    public function studentStatistics()
    {
        return $this->hasMany(StudentStatistic::class);
    }

    public function settings()
    {
        return $this->belongsTo(Setting::class);
    }
}

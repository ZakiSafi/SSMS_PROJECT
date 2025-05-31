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
        'type',
        'province_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

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

    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }
}

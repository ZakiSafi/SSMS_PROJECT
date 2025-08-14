<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Auth;

class University extends Model
{
    use HasFactory, HasRoles;

    protected $fillable = [
        'name',
        'type',
        'province_id',
    ];

    protected static function booted()
    {
        static::addGlobalScope('university', function ($query) {
            if (!Auth::check() || Auth::user()->hasRole('admin')) return;
            $query->where('id', Auth::user()->university_id);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    // Many-to-Many: University has many faculties
    public function faculties()
    {
        return $this->belongsToMany(Faculty::class, 'faculty_university');
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

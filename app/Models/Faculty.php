<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Faculty extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    // Many-to-Many: Faculty belongs to multiple universities
    public function universities(): BelongsToMany
    {
        return $this->belongsToMany(University::class, 'faculty_university');
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function studentStatistics(): HasMany
    {
        return $this->hasMany(StudentStatistic::class);
    }
}

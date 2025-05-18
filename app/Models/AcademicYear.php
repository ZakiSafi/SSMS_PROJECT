<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{

    use HasFactory;
    protected $fillable = ['year', 'label'];

    public function StudentStatistics(): HasMany
    {
        return $this->hasMany(StudentStatistic::class);
    }
}

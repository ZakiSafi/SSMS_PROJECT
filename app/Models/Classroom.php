<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Classroom extends Model
{
    protected $fillable = [
        'name',
    ];
    public function StudentStatistics(): HasMany
    {
        return $this->hasMany(StudentStatistic::class);
    }
}

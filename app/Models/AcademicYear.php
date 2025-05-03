<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    protected $fillable = ['years','lable'];

    public function StudentStatistics():HasMany
    {
        return $this->hasMany(StudentStatistic::class);
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;

class StudentStatistic extends Model
{
    use HasFactory, HasRoles;

    protected $fillable = [
        'academic_year',
        'university_id',
        'faculty_id',
        'department_id',
        'classroom',
        'semester_number',
        'shift',
        'season',
        'male_total',
        'female_total',
        'student_type',
    ];

    protected static function booted()
{
    static::addGlobalScope('university', function ($query) {
        if (!Auth::check() ||Auth::user()->hasRole('admin')) {
            return;
        }

        $query->where('university_id', Auth::user()->university_id);
    });
}


    public function university()
    {
        return $this->belongsTo(University::class);
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

   
}

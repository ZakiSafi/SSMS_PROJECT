<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentStatistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'university_id',
        'faculty_id',
        'department_id',
        'classroom_id',
        'male_total',
        'female_total',
        'student_type',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
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

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
}

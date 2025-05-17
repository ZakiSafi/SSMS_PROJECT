<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\StudentStatistic;
use Illuminate\Support\Facades\DB;

class ClassRoomReportController extends Controller
{
    public function __invoke()
    {
        $report = StudentStatistic::join('academic_years', 'student_statistics.academic_year_id', '=', 'academic_years.id')
            ->join('universities', 'student_statistics.university_id', '=', 'universities.id')
            ->join('faculties', 'student_statistics.faculty_id', '=', 'faculties.id')
            ->join('departments', 'student_statistics.department_id', '=', 'departments.id')
            ->join('classrooms', 'student_statistics.classroom_id', '=', 'classrooms.id')
            ->select(
                'academic_years.year as year',
                'universities.name as university',
                'faculties.name as faculty',
                'departments.name as department',
                'classrooms.name as classroom',
                'classrooms.male_students as total_males',
                'classrooms.female_students as total_females',
                DB::raw('classrooms.male_students + classrooms.female_students as total_students')
            )
            ->orderBy('academic_years.year')
            ->get();

        return response()->json($report);
    }
}

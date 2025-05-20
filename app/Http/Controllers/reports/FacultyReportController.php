<?php

namespace App\Http\Controllers\reports;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentStatistic;

use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\select;

class FacultyReportController extends Controller
{
    public function __invoke()
    {
        $report = StudentStatistic::join('academic_years', 'student_statistics.academic_year', '=', 'academic_years.id')
        ->join('universities', 'student_statistics.university_id', '=', 'universities.id')
        ->join('faculties', 'student_statistics.faculty_id','=', 'faculties.id')
        ->select(
            'academic_years.year as year',
            'universities.name as university',
            'faculties.name as faculty',
            DB::raw('Sum(male_total) as total_males'),
            DB::raw('Sum(female_total) as total_females'),
            DB::raw('Sum(male_total + female_total) as total_students')
        )
        ->groupBy(
            'academic_years.year',
            'universities.name',
            'faculties.name',
        )
        ->orderBy('academic_years.year')
        ->get()
        ->groupBy('university', 'year');

        return response()->json($report);

    }
}

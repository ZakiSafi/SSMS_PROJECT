<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\StudentStatistic;
use Illuminate\Support\Facades\DB;

class FacultyBaseGraduationReportController extends Controller
{
    public function __invoke(Request $request)
    {
        $year = $request->query('year');
        $season = $request->query('season');
        $shift = $request->query('shift');
        $type = 'graduated';


        $query = StudentStatistic::join('universities', 'student_statistics.university_id', '=', 'universities.id')
            ->join('faculties', 'student_statistics.faculty_id', '=', 'faculties.id')
            ->select(
                'student_statistics.academic_year as year',
                'student_statistics.season as season',
                'student_statistics.shift as shift',
                "student_statistics.student_type as type",
                'universities.name as university',
                'faculties.name as faculty',
                DB::raw('SUM(student_statistics.male_total) as Total_Males'),
                DB::raw('SUM(student_statistics.female_total) as Total_Females'),
                DB::raw('SUM(student_statistics.female_total + student_statistics.male_total) as Total_Students'),
                DB::raw('ROUND((SUM(male_total) / NULLIF(SUM(male_total + female_total), 0)) * 100, 0) as Male_Percentage'),
                DB::raw('ROUND((SUM(female_total) / NULLIF(SUM(male_total + female_total), 0)) * 100, 0) as Female_Percentage'),
            );
        if ($year && $year !== 'all') {
            $query->whereYear('student_statistics.academic_year', $year);
        }
        if ($season && $season !== 'all') {
            $query->where('student_statistics.season', $season);
        }
        if ($shift && $shift !== 'all') {
            $query->where('student_statistics.shift', $shift);
        }
        $statistics = $query->where('student_statistics.student_type', $type)
            ->groupBy(
                'student_statistics.academic_year',
                'student_statistics.student_type',
                'student_statistics.season',
                'student_statistics.shift',
                'universities.name',
                'faculties.name'
            )
            ->get();

        return response()->json($statistics);
    }
}

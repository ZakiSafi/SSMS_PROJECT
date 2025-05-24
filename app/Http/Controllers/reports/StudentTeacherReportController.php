<?php

namespace App\Http\Controllers\reports;
use App\Http\Controllers\Controller;

use App\Models\StudentStatistic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentTeacherReportController extends Controller
{
    public function __invoke()
    {
        $year = request()->query('year');
        $query = StudentStatistic::join('universities', 'student_statistics.university_id', '=', 'universities.id')
            ->select(
                'student_statistics.academic_year as year',
                'universities.name as university',
                'universities.teachers as teachers',
                DB::raw('SUM(student_statistics.male_total + female_total) as Total_Students'),
                DB::raw('SUM(student_statistics.male_total + female_total) / NULLIF(universities.teachers, 0) as Students_Per_Teacher_Ratio')


            );
            if ($year && $year !== 'all') {
                $query->whereYear('student_statistics.academic_year', $year);
            }
            $statistics = $query
            ->groupBy(
                'student_statistics.academic_year',
                'universities.name',
                'universities.teachers'
            )
            ->get();
        return response()->json($statistics);

    }
}

<?php

namespace App\Http\Controllers\reports;
use Illuminate\Http\Request;
use App\Models\StudentStatistic;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class UniversityBasedGraduationReportController extends Controller
{
    public function __invoke(Request $request)
    {
        $season  = $request->query('season');
        $year = $request->query('year');
        $perPage = $request->query('perPage', 10);

        $query = StudentStatistic::join('universities', 'student_statistics.university_id', '=', 'universities.id')
            ->select(
                'student_statistics.academic_year as year',
                'universities.name as university',
                'student_statistics.season',
                DB::raw('SUM(male_total) as Total_Males'),
                DB::raw('SUM(female_total) as Total_Females'),
                DB::raw('SUM(male_total + female_total) as Total_Students'),
                DB::raw('ROUND((SUM(male_total) / NULLIF(SUM(male_total + female_total), 0)) * 100, 0) as Male_Percentage'),
                DB::raw('ROUND((SUM(female_total) / NULLIF(SUM(male_total + female_total), 0)) * 100, 0) as Female_Percentage'),)
            ->where('student_statistics.academic_year', $year)
            ->where('student_statistics.season', $season)
            ->where('student_statistics.student_type', 'graduated')
            ->groupBy(
                'student_statistics.academic_year',
                'universities.name',
                'student_statistics.season'

            )
            ->paginate($perPage);
            return response()->json($query);

    }

}

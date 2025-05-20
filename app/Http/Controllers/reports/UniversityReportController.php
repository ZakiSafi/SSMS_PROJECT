<?php

namespace App\Http\Controllers\reports;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\StudentStatistic;
use Illuminate\Support\Facades\DB;
// use App\Models\StudentStatistic;
class UniversityReportController extends Controller
{


    public function __invoke()
    {
        $statistics = StudentStatistic::join('universities', 'student_statistics.university_id', '=', 'universities.id')
            ->join('academic_years', 'student_statistics.academic_year', '=', 'academic_years.id')
            ->select(
                'academic_years.year as academic_year',
                'universities.name as university',
                DB::raw('SUM(male_total + female_total) as total_students')
            )
            ->groupBy('academic_years.year', 'universities.name')
            ->get();

        return response()->json($statistics);
    }
}

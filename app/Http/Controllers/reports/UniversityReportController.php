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
        $shift = request()->query('shift');
        $statistics = StudentStatistic::join('universities', 'student_statistics.university_id', '=', 'universities.id')
            ->select(
                'student_statistics.academic_year as year',
                'universities.name as university',
                'student_statistics.shift',
                DB::raw('SUM(male_total) as Total_Males'),
                DB::raw('SUM(female_total) as Total_Females'),
                DB::raw('SUM(male_total + female_total) as Total_Students')
            )
            ->where('student_statistics.shift', $shift)
            ->groupBy('student_statistics.academic_year','universities.name','student_statistics.shift')
            ->get()
            ->groupBy('university');

        return response()->json($statistics);
    }
}

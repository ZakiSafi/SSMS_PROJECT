<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentStatistic;
use Illuminate\Support\Facades\DB;

class UniversityReportController extends Controller
{
    public function __invoke(Request $request)
    {
        $shift = $request->query('shift');

        $query = StudentStatistic::join('universities', 'student_statistics.university_id', '=', 'universities.id')
            ->select(
                'student_statistics.academic_year as year',
                'universities.name as university',
                DB::raw('SUM(male_total) as Total_Males'),
                DB::raw('SUM(female_total) as Total_Females'),
                DB::raw('SUM(male_total + female_total) as Total_Students')
            );

        // If specific shift (day or night) is requested, apply it
        if ($shift && $shift !== 'all') {
            $query->where('student_statistics.shift', $shift);
        }

        $statistics = $query
            ->groupBy('student_statistics.academic_year', 'universities.name')
            ->get();

        return response()->json($statistics);
    }
}

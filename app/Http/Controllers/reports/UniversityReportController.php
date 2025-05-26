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
        $year = $request->query('year');
        $shift = $request->query('shift');
        $perPage = $request->query('per_page', 10);

        $query = StudentStatistic::join('universities', 'student_statistics.university_id', '=', 'universities.id')
            ->select(
                'student_statistics.academic_year as year',
                'universities.name as university',
                DB::raw('SUM(male_total) as Total_Males'),
                DB::raw('SUM(female_total) as Total_Females'),
                DB::raw('SUM(male_total + female_total) as Total_Students'),
            DB::raw('ROUND((SUM(male_total) / NULLIF(SUM(male_total + female_total), 0)) * 100, 0) as Male_Percentage'),
            DB::raw('ROUND((SUM(female_total) / NULLIF(SUM(male_total + female_total), 0)) * 100, 0) as Female_Percentage'),


        );

        if ($shift && $shift !== 'all') {
            $query->where('student_statistics.shift', $shift);
        }

        if ($year && $year !== 'all') {
            $query->where('student_statistics.academic_year', $year);
        }

        $query->groupBy('student_statistics.academic_year', 'universities.name');

        // Pagination applied here
        $statistics = $query->paginate($perPage);

        return response()->json($statistics);
    }
}

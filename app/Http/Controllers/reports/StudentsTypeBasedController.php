<?php

namespace App\Http\Controllers\reports;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\StudentStatistic;
use Illuminate\Support\Facades\DB;


class StudentsTypeBasedController extends Controller
{
    public function __invoke(Request $request)
    {
        $year = $request->query('year');
        $shift = $request->query('shift');
        $type = $request->query('type');

        $query = StudentStatistic::join('universities', 'student_statistics.university_id', '=', 'universities.id')
            ->join('faculties', 'student_statistics.faculty_id', '=', 'faculties.id')
            ->select(
                'student_statistics.academic_year as year',
                'universities.name as university',
                'faculties.name as faculty',
                'student_statistics.shift as shift',
                'student_statistics.student_type as type',
                DB::raw('SUM(male_total) as Total_Males'),
                DB::raw('SUM(female_total) as Total_Females'),
                DB::raw('SUM(male_total + female_total) as Total_Students'),
            );
            if ($shift && $shift !== 'all') {
            $query->where('student_statistics.shift', $shift);
            };

            if ($year && $year !== 'all') {
            $query->whereYear('student_statistics.academic_year', $year);
            };

            if ($type && $type !== 'all') {
            $query->where('student_statistics.student_type', $type);
            }
            $statistics = $query->groupBy('student_statistics.academic_year', 'universities.name', 'faculties.name', 'student_statistics.shift',
            'student_statistics.student_type')
            ->orderBy('student_statistics.academic_year', 'desc')
            ->get();
            if(!$statistics){
                return response()->json([
                    'message' => 'No data found for the given criteria.'
                ], 404);

            }
            return response()->json($statistics);
    }
}

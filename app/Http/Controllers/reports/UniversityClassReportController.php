<?php

namespace App\Http\Controllers\reports;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class UniversityClassReportController extends Controller
{
    public function __invoke(Request $request)
    {
        $year = $request->query('year');
        $shift = $request->query('shift');

        $data = DB::table('student_statistics')
            ->join('universities', 'student_statistics.university_id', '=', 'universities.id')
            ->select(
                'student_statistics.university_id',
                'universities.name as university_name',
                'student_statistics.classroom',
                'student_statistics.shift',
                DB::raw('SUM(student_statistics.male_total) as Total_males'),
                DB::raw('SUM(student_statistics.female_total) as Total_Females'),
                DB::raw('SUM(student_statistics.male_total + student_statistics.female_total) as Total_Students')
            )
            ->whereYear('student_statistics.academic_year', $year)
            ->where('student_statistics.shift', $shift)
            ->groupBy(
                'student_statistics.university_id',
                'universities.name',
                'student_statistics.classroom',
                'student_statistics.shift'
            )
            ->get();

        return response()->json($data);
    }
}

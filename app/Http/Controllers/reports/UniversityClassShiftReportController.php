<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\StudentStatistic;
use Illuminate\Support\Facades\DB;

class ClassRoomReportController extends Controller
{
    public function __invoke(Request $request)
    {
        $year = $request->query('year');
        $data = DB::table('student_statistics')
            ->join('departments', 'student_statistics.department_id', '=', 'departments.id')
            ->join('faculties', 'departments.faculty_id', '=', 'faculties.id')
            ->join('universities', 'faculties.university_id', '=', 'universities.id')
            ->select(
                'universities.id as university_id',
                'universities.name as university_name',
                'student_statistics.classroom',
                'student_statistics.shift',
                DB::raw('SUM(student_statistics.male_total + student_statistics.female_total) as total_students')
            )
            ->whereYear('student_statistics.academic_year', $year)
            ->groupBy('universities.id', 'universities.name', 'student_statistics.classroom', 'student_statistics.shift')
            ->get();

        return response()->json($data);
    }
}

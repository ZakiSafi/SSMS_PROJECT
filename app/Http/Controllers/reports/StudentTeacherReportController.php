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
        $perPage = request()->query('perPage', 10);

        $query = DB::table('student_statistics')
            ->join('universities', 'student_statistics.university_id', '=', 'universities.id')
            ->join('teachers', function ($join) {
                $join->on('student_statistics.university_id', '=', 'teachers.university_id')
                    ->on('student_statistics.academic_year', '=', 'teachers.academic_year');
            })
            ->select(
                'student_statistics.academic_year as year',
                'universities.name as university',
                'teachers.total_teachers as teachers',
                DB::raw('SUM(student_statistics.male_total + student_statistics.female_total) as total_students'),
                DB::raw('ROUND(SUM(student_statistics.male_total + student_statistics.female_total) / NULLIF(teachers.total_teachers, 0), 2) as students_per_teacher_ratio')
            );

        if ($year && $year !== 'all') {
            $query->where('student_statistics.academic_year', $year);
        }

        $statistics = $query
            ->groupBy(
                'student_statistics.academic_year',
                'universities.name',
                'teachers.total_teachers'
            )
            ->orderBy('student_statistics.academic_year', 'desc')
            ->paginate($perPage);

        return response()->json($statistics);
    }
}

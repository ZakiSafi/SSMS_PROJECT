<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentStatistic;
use Illuminate\Support\Facades\DB;

class DepartmentClassBasedController extends Controller
{
    public function __invoke(Request $request)
    {
        $year = $request->query('year');
        $season = $request->query('season');
        $query = StudentStatistic::join('universities', 'student_statistics.university_id', '=', 'universities.id')
            ->join('faculties', 'student_statistics.faculty_id', '=', 'faculties.id')
            ->join('departments', 'student_statistics.department_id', '=', 'departments.id')
            ->select(
                'universities.name as university',
                'faculties.name as faculty',
                'departments.name as department',
                'student_statistics.classroom as class',
                'student_statistics.season as semester',
                'student_statistics.shift as shift',
                DB::raw('Sum(student_statistics.male_total) as Total_Male'),
                DB::raw('Sum(student_statistics.female_total) as Total_Female'),
                DB::raw('Sum(student_statistics.male_total + student_statistics.female_total) as Total_Students'),
                DB::raw('ROUND((SUM(male_total) / NULLIF(SUM(male_total + female_total), 0)) * 100, 0) as Male_Percentage'),
                DB::raw('ROUND((SUM(female_total) / NULLIF(SUM(male_total + female_total), 0)) * 100, 0) as Female_Percentage'),
            )
            ->whereYear('student_statistics.academic_year', $year)
            ->where('student_statistics.season', $season)
            ->groupBy(
                'universities.name',
                'faculties.name',
                'departments.name',
                'student_statistics.classroom',
                'student_statistics.shift',
                'student_statistics.season'
            )
            ->get();
        return response()->json($query);
    }
}

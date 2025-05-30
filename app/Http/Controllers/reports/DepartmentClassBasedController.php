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
        $perPage = $request->query('perPage', 10);

        // STEP 1: Paginate departments based on filters
        $departmentPaginator = DB::table('student_statistics')
            ->join('universities', 'student_statistics.university_id', '=', 'universities.id')
            ->join('faculties', 'student_statistics.faculty_id', '=', 'faculties.id')
            ->join('departments', 'student_statistics.department_id', '=', 'departments.id')
            ->select(
                'student_statistics.department_id',
                'universities.name as university_name',
                'faculties.name as faculty_name',
                'departments.name as department_name'
            )
            ->where('student_statistics.academic_year', $year)
            ->where('student_statistics.season', $season)
            ->groupBy(
                'student_statistics.department_id',
                'universities.name',
                'faculties.name',
                'departments.name'
            )
            ->paginate($perPage);

        $departmentIds = $departmentPaginator->pluck('department_id')->toArray();

        // STEP 2: Fetch all classes for the paginated departments
        $classResults = DB::table('student_statistics')
            ->join('universities', 'student_statistics.university_id', '=', 'universities.id')
            ->join('faculties', 'student_statistics.faculty_id', '=', 'faculties.id')
            ->join('departments', 'student_statistics.department_id', '=', 'departments.id')
            ->select(
                'student_statistics.department_id',
                'universities.name as university_name',
                'faculties.name as faculty_name',
                'departments.name as department_name',
                'student_statistics.classroom',
                'student_statistics.shift',
                DB::raw('SUM(student_statistics.male_total) as Total_males'),
                DB::raw('SUM(student_statistics.female_total) as Total_Females'),
                DB::raw('SUM(student_statistics.male_total + student_statistics.female_total) as Total_Students'),
                DB::raw('ROUND((SUM(male_total) / NULLIF(SUM(male_total + female_total), 0)) * 100, 0) as Male_Percentage'),
                DB::raw('ROUND((SUM(female_total) / NULLIF(SUM(male_total + female_total), 0)) * 100, 0) as Female_Percentage')
            )
            ->where('student_statistics.academic_year', $year)
            ->where('student_statistics.season', $season)
            ->whereIn('student_statistics.department_id', $departmentIds)
            ->groupBy(
                'student_statistics.department_id',
                'universities.name',
                'faculties.name',
                'departments.name',
                'student_statistics.classroom',
                'student_statistics.shift'
            )
            ->get();

        // STEP 3: Get all unique classrooms from this result set
        $allClasses = $classResults
            ->pluck('classroom')
            ->filter(fn($classroom) => trim($classroom) !== '')
            ->unique()
            ->sort()
            ->values();

        // STEP 4: Group class results under each department
        $grouped = [];

        foreach ($departmentPaginator as $department) {
            $grouped[$department->department_id] = [
                'university' => $department->university_name,
                'faculty' => $department->faculty_name,
                'department' => $department->department_name,
                'classes' => []
            ];

            // Pre-fill classes with null
            foreach ($allClasses as $class) {
                $grouped[$department->department_id]['classes'][$class] = null;
            }
        }

        foreach ($classResults as $row) {
            $grouped[$row->department_id]['classes'][$row->classroom] = [
                'shift' => $row->shift,
                'Total_males' => (string) $row->Total_males,
                'Total_Females' => (string) $row->Total_Females,
                'Total_Students' => (string) $row->Total_Students,
                'Male_Percentage' => (string) $row->Male_Percentage,
                'Female_Percentage' => (string) $row->Female_Percentage
            ];
        }

        // STEP 5: Return final JSON with pagination
        return response()->json([
            'data' => array_values($grouped),
            'current_page' => $departmentPaginator->currentPage(),
            'last_page' => $departmentPaginator->lastPage(),
            'per_page' => $departmentPaginator->perPage(),
            'total' => $departmentPaginator->total(),
        ]);
    }
}

<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepartmentClassBasedController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole('admin');
        $year = $request->query('year');
        $season = $request->query('season');
        $universityName = $request->query('university');
        $perPage = $request->query('perPage', 10);

        // STEP 1: Paginate departments
        $departmentPaginator = DB::table('student_statistics')
            ->join('universities', 'student_statistics.university_id', '=', 'universities.id')
            ->join('faculties', 'student_statistics.faculty_id', '=', 'faculties.id')
            ->join('departments', 'student_statistics.department_id', '=', 'departments.id')
            ->select(
                'student_statistics.university_id',
                'universities.name as university_name',
                'student_statistics.faculty_id',
                'faculties.name as faculty_name',
                'student_statistics.department_id',
                'departments.name as department_name'
            )
            ->when(!$isAdmin && $user->university_id, function ($query) use ($user) {
                return $query->where('student_statistics.university_id', $user->university_id);
            })
            ->where('student_statistics.academic_year', $year)
            ->when($season !== 'all', function ($query) use ($season) {
                return $query->where('student_statistics.season', $season);
            })
            ->when($universityName !== 'all', function ($query) use ($universityName) {
                return $query->where('universities.id', $universityName);
            })
            ->groupBy(
                'student_statistics.university_id',
                'universities.name',
                'student_statistics.faculty_id',
                'faculties.name',
                'student_statistics.department_id',
                'departments.name'
            )
            ->paginate($perPage);

        $departmentIds = $departmentPaginator->pluck('department_id')->toArray();

        // STEP 2: Fetch class statistics for departments
        $classResults = DB::table('student_statistics')
            ->join('universities', 'student_statistics.university_id', '=', 'universities.id')
            ->join('faculties', 'student_statistics.faculty_id', '=', 'faculties.id')
            ->join('departments', 'student_statistics.department_id', '=', 'departments.id')
            ->select(
                'student_statistics.university_id',
                'universities.name as university_name',
                'student_statistics.faculty_id',
                'faculties.name as faculty_name',
                'student_statistics.department_id',
                'departments.name as department_name',
                'student_statistics.classroom',
                'student_statistics.shift',
                DB::raw('SUM(male_total) as Total_males'),
                DB::raw('SUM(female_total) as Total_Females'),
                DB::raw('SUM(male_total + female_total) as Total_Students'),
                DB::raw('ROUND((SUM(male_total) / NULLIF(SUM(male_total + female_total), 0)) * 100, 0) as Male_Percentage'),
                DB::raw('ROUND((SUM(female_total) / NULLIF(SUM(male_total + female_total), 0)) * 100, 0) as Female_Percentage')
            )
            ->where('student_statistics.academic_year', $year)
            ->when($season !== 'all', function ($query) use ($season) {
                return $query->where('student_statistics.season', $season);
            })
            ->when($universityName !== 'all', function ($query) use ($universityName) {
                return $query->where('universities.id', $universityName);
            })
            ->whereIn('student_statistics.department_id', $departmentIds)
            ->groupBy(
                'student_statistics.university_id',
                'universities.name',
                'student_statistics.faculty_id',
                'faculties.name',
                'student_statistics.department_id',
                'departments.name',
                'student_statistics.classroom',
                'student_statistics.shift'
            )
            ->get();

        // STEP 3: Get unique class names
        $allClasses = $classResults->pluck('classroom')->filter()->unique()->sort()->values();

        // STEP 4: Build nested structure - FIXED VERSION
        $universities = [];
        $facultiesMap = [];
        $departmentsMap = [];

        foreach ($departmentPaginator as $dept) {
            $uniId = $dept->university_id;
            $facId = $dept->faculty_id;
            $deptId = $dept->department_id;

            // Create university if not exists
            if (!isset($universities[$uniId])) {
                $universities[$uniId] = [
                    'university' => $dept->university_name,
                    'faculties' => []
                ];
            }

            // Create faculty if not exists
            if (!isset($facultiesMap[$facId])) {
                $facultiesMap[$facId] = [
                    'faculty' => $dept->faculty_name,
                    'departments' => []
                ];
                $universities[$uniId]['faculties'][] = &$facultiesMap[$facId];
            }

            // Create department if not exists
            if (!isset($departmentsMap[$deptId])) {
                $departmentsMap[$deptId] = [
                    'department' => $dept->department_name,
                    'classes' => collect($allClasses)->mapWithKeys(fn($class) => [$class => null])->toArray()
                ];
                $facultiesMap[$facId]['departments'][] = &$departmentsMap[$deptId];
            }
        }

        // Fill in classes
        foreach ($classResults as $row) {
            foreach ($departmentsMap as &$department) {
                if ($department['department'] === $row->department_name) {
                    $department['classes'][$row->classroom] = [
                        'shift' => $row->shift,
                        'Total_males' => (string) $row->Total_males,
                        'Total_Females' => (string) $row->Total_Females,
                        'Total_Students' => (string) $row->Total_Students,
                        'Male_Percentage' => (string) $row->Male_Percentage,
                        'Female_Percentage' => (string) $row->Female_Percentage,
                    ];
                    break;
                }
            }
        }

        // Convert to sequential arrays
        $final = array_values(array_map(function($uni) {
            $uni['faculties'] = array_values(array_map(function($fac) {
                $fac['departments'] = array_values($fac['departments']);
                return $fac;
            }, $uni['faculties']));
            return $uni;
        }, $universities));

        // STEP 5: Return result
        return response()->json([
            'data' => $final,
            'current_page' => $departmentPaginator->currentPage(),
            'last_page' => $departmentPaginator->lastPage(),
            'per_page' => $departmentPaginator->perPage(),
            'total' => $departmentPaginator->total(),
        ]);
    }
}
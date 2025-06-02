<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentClassBasedController extends Controller
{
    public function __invoke(Request $request)
    {
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
            ->where('student_statistics.academic_year', $year)
            ->when($season !== 'all', function ($query) use ($season) {
                return $query->where('student_statistics.season', $season);
            })
            ->when($universityName !== 'all', function ($query) use ($universityName) {
                return $query->where('universities.name', $universityName);
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

        // STEP 4: Build nested structure
        $grouped = [];

        foreach ($departmentPaginator as $dept) {
            $uniId = $dept->university_id;
            $facId = $dept->faculty_id;
            $deptId = $dept->department_id;

            // University block
            if (!isset($grouped[$uniId])) {
                $grouped[$uniId] = [
                    'university' => $dept->university_name,
                    'faculties' => []
                ];
            }

            // Faculty block
            if (!isset($grouped[$uniId]['faculties'][$facId])) {
                $grouped[$uniId]['faculties'][$facId] = [
                    'faculty' => $dept->faculty_name,
                    'departments' => []
                ];
            }

            // Department block
            $grouped[$uniId]['faculties'][$facId]['departments'][$deptId] = [
                'department' => $dept->department_name,
                'classes' => collect($allClasses)->mapWithKeys(fn($class) => [$class => null])->toArray()
            ];
        }

        // Fill in classes
        foreach ($classResults as $row) {
            $grouped[$row->university_id]['faculties'][$row->faculty_id]['departments'][$row->department_id]['classes'][$row->classroom] = [
                'shift' => $row->shift,
                'Total_males' => (string) $row->Total_males,
                'Total_Females' => (string) $row->Total_Females,
                'Total_Students' => (string) $row->Total_Students,
                'Male_Percentage' => (string) $row->Male_Percentage,
                'Female_Percentage' => (string) $row->Female_Percentage,
            ];
        }

        // Flatten faculty and department indexes to numeric arrays
        $final = array_map(function ($uni) {
            $uni['faculties'] = array_values(array_map(function ($fac) {
                $fac['departments'] = array_values($fac['departments']);
                return $fac;
            }, $uni['faculties']));
            return $uni;
        }, array_values($grouped));

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

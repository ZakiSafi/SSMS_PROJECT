<?php

namespace App\Http\Controllers\reports;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UniversityClassReportController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole('Admin');
        $year = $request->query('year');
        $shift = $request->query('shift');
        $perPage = $request->query('perPage', 10);

        // STEP 1: Paginate universities based on filters
        $universityPaginator = DB::table('student_statistics')
            ->join('universities', 'student_statistics.university_id', '=', 'universities.id')
            ->select('student_statistics.university_id', 'universities.name as university_name')
            ->where('student_statistics.academic_year', $year)
            ->where('student_statistics.shift', $shift)
            ->when(!$isAdmin, function ($query) use ($user) {
                return $query->where('student_statistics.university_id', $user->university_id);
            })
            ->groupBy('student_statistics.university_id', 'universities.name')
            ->paginate($perPage);

        $universityIds = $universityPaginator->pluck('university_id')->toArray();

        // STEP 2: Fetch all classes for the paginated universities
        $classResults = DB::table('student_statistics')
            ->join('universities', 'student_statistics.university_id', '=', 'universities.id')
            ->select(
                'student_statistics.university_id',
                'universities.name as university_name',
                'student_statistics.classroom',

                DB::raw('SUM(student_statistics.male_total) as Total_males'),
                DB::raw('SUM(student_statistics.female_total) as Total_Females'),
                DB::raw('SUM(student_statistics.male_total + student_statistics.female_total) as Total_Students')
            )
            ->where('student_statistics.academic_year', $year)
            ->where('student_statistics.shift', $shift)
            ->whereIn('student_statistics.university_id', $universityIds)
            ->groupBy(
                'student_statistics.university_id',
                'universities.name',
                'student_statistics.classroom'
            )
            ->get();

        // STEP 3: Get all unique classrooms from this result set
        $allClasses = $classResults
            ->pluck('classroom')
            ->filter(fn($classroom) => trim($classroom) !== '')
            ->unique()
            ->sort()
            ->values();

        // STEP 4: Group class results under each university
        $grouped = [];

        foreach ($universityPaginator as $university) {
            $grouped[$university->university_id] = [
                'university_id' => $university->university_id,
                'university_name' => $university->university_name,
                'classes' => []
            ];

            // Pre-fill classes with null
            foreach ($allClasses as $class) {
                $grouped[$university->university_id]['classes'][$class] = null;
            }
        }

        foreach ($classResults as $row) {
            $grouped[$row->university_id]['classes'][$row->classroom] = [
                'Total_males' => (string) $row->Total_males,
                'Total_Females' => (string) $row->Total_Females,
                'Total_Students' => (string) $row->Total_Students,
            ];
        }

        // STEP 5: Return final JSON with pagination
        return response()->json([
            'data' => array_values($grouped),
            'current_page' => $universityPaginator->currentPage(),
            'last_page' => $universityPaginator->lastPage(),
            'per_page' => $universityPaginator->perPage(),
            'total' => $universityPaginator->total(),
        ]);
    }
}

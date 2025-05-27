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
         $perPage = $request->query('perPage', 10);

        $results = DB::table('student_statistics')
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
            ->groupBy(
                'student_statistics.university_id',
                'universities.name',
                'student_statistics.classroom'
            )
            ->paginate($perPage);

        // Step 1: Get all unique classrooms
        $allClasses = collect($results)
            ->pluck('classroom')
            ->filter(function ($classroom) {
                return trim($classroom) !== '';
            })
            ->unique()
            ->sort()
            ->values();


        // Step 2: Group by university
        $grouped = [];

        foreach ($results as $row) {
            $universityId = $row->university_id;

            // Initialize university block if not already
            if (!isset($grouped[$universityId])) {
                $grouped[$universityId] = [
                    'university_id' => $universityId,
                    'university_name' => $row->university_name,
                    'classes' => []
                ];

                // Pre-fill all classes with null
                foreach ($allClasses as $class) {
                    $grouped[$universityId]['classes'][$class] = null;
                }
            }

            // Fill in class info
            $grouped[$universityId]['classes'][$row->classroom] = [
                'Total_males' => (string) $row->Total_males,
                'Total_Females' => (string) $row->Total_Females,
                'Total_Students' => (string) $row->Total_Students,
            ];
        }

        // Return as indexed array
        return response()->json(array_values($grouped));
    }
}

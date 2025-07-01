<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentStatistic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepartmentBasedGraduationReportController extends Controller
{

    public function __invoke(Request $request)
    {
        $user = Auth::user();
        $year = $request->query('year');
        $season = $request->query('season');
        $shift = $request->query('shift');
        $perPage = $request->query('perPage', 10);
        $type = 'graduated';

        $query = StudentStatistic::join('universities', 'student_statistics.university_id', '=', 'universities.id')
            ->join('faculties', 'student_statistics.faculty_id', '=', 'faculties.id')
            ->join('departments', 'student_statistics.department_id', '=', 'departments.id')
            ->select(
                'student_statistics.academic_year as year',
                'student_statistics.season as season',
                'student_statistics.shift as shift',
                'student_statistics.student_type as type',
                'universities.name as university',
                'faculties.name as faculty',
                'departments.name as department',
                DB::raw('SUM(student_statistics.male_total) as Total_Males'),
                DB::raw('SUM(student_statistics.female_total) as Total_Females'),
                DB::raw('SUM(student_statistics.female_total + student_statistics.male_total) as Total_Students'),
                DB::raw('ROUND((SUM(male_total) / NULLIF(SUM(male_total + female_total), 0)) * 100, 0) as Male_Percentage'),
                DB::raw('ROUND((SUM(female_total) / NULLIF(SUM(male_total + female_total), 0)) * 100, 0) as Female_Percentage')
            );
            if (!$user->hasRole('Admin')) {
    $query->where('student_statistics.university_id', $user->university_id);
}
        if ($season && $season !== 'all') {
            $query->whereRaw('LOWER(student_statistics.season) = ?', [strtolower(trim($season))]);
        }
        if ($shift && $shift !== 'all') {
            $query->whereRaw('LOWER(student_statistics.shift) = ?', [strtolower(trim($shift))]);
        }
        if ($year && $year !== 'all') {
            $query->where('student_statistics.academic_year', intval($year));
        }


        $statistics = $query->where('student_statistics.student_type', $type)
            ->groupBy(
                'student_statistics.academic_year',
                'student_statistics.student_type',
                'student_statistics.season',
                'student_statistics.shift',
                'universities.name',
                'faculties.name',
                'departments.name'
            )
            ->paginate($perPage);

        // Group by university, then faculty, then list departments
        $grouped = $statistics->groupBy('university')->map(function ($items, $universityName) {
            return [
                'university' => $universityName,
                'faculties' => $items->groupBy('faculty')->map(function ($facultyItems, $facultyName) {
                    return [
                        'faculty' => $facultyName,
                        'departments' => $facultyItems->map(function ($item) {
                            return [
                                'department' => $item->department,
                                'year' => $item->year,
                                'season' => $item->season,
                                'shift' => $item->shift,
                                'type' => $item->type,
                                'Total_Males' => $item->Total_Males,
                                'Total_Females' => $item->Total_Females,
                                'Total_Students' => $item->Total_Students,
                                'Male_Percentage' => $item->Male_Percentage,
                                'Female_Percentage' => $item->Female_Percentage,
                            ];
                        })->values()
                    ];
                })->values()
            ];
        })->values();

        if ($grouped->isEmpty()) {
            return response()->json(['message' => 'No data found'], 404);
        }

        // Return both data and pagination info
        return response()->json([
            'data' => $grouped,
            'pagination' => [
                'total' => $statistics->total(),
                'per_page' => $statistics->perPage(),
                'current_page' => $statistics->currentPage(),
                'last_page' => $statistics->lastPage(),
                'from' => $statistics->firstItem(),
                'to' => $statistics->lastItem(),
            ],
        ]);
    }
}

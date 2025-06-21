<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentStatistic;
use Illuminate\Support\Facades\DB;

class StudentsTypeBasedController extends Controller
{
    public function __invoke(Request $request)
    {
        $year = $request->query('year');
        $shift = $request->query('shift');
        $perPage = $request->query('perPage', 10);

        // Fetch all types in one query
        $query = StudentStatistic::join('universities', 'student_statistics.university_id', '=', 'universities.id')
            ->join('faculties', 'student_statistics.faculty_id', '=', 'faculties.id')
            ->select(
                'student_statistics.faculty_id',
                'student_statistics.university_id',
                'student_statistics.academic_year as year',
                'student_statistics.shift as shift',
                'student_statistics.student_type as type',
                'universities.name as university',
                'faculties.name as faculty',
                DB::raw('SUM(student_statistics.male_total) as males'),
                DB::raw('SUM(student_statistics.female_total) as females'),
                DB::raw('SUM(student_statistics.female_total + student_statistics.male_total) as total')
            );

        if ($shift && $shift !== 'all') {
            $query->whereRaw('LOWER(student_statistics.shift) = ?', [strtolower(trim($shift))]);
        }

        if ($year && $year !== 'all') {
            $query->where('student_statistics.academic_year', intval($year));
        }

        $data = $query
            ->groupBy(
                'student_statistics.academic_year',
                'student_statistics.student_type',
                'student_statistics.shift',
                'student_statistics.faculty_id',
                'student_statistics.university_id',
                'faculties.name',
                'universities.name'
            )
            ->get();

        // Group by university and faculty
        $grouped = $data->groupBy('university')->map(function ($facultyStats, $university) {
            return [
                'university' => $university,
                'faculties' => $facultyStats->groupBy('faculty')->map(function ($types) {
                    $result = [
                        'faculty' => $types->first()->faculty,
                        'students' => [
                            'newly_enrolled' => ['males' => 0, 'females' => 0, 'total' => 0],
                            'graduated' => ['males' => 0, 'females' => 0, 'total' => 0],
                            'current' => ['males' => 0, 'females' => 0, 'total' => 0],
                        ]
                    ];

                    foreach ($types as $type) {
                        if ($type->type === 'current') {
                            $result['students']['current'] = [
                                'males' => $type->males,
                                'females' => $type->females,
                                'total' => $type->total,
                            ];
                        } elseif ($type->type === 'graduated') {
                            $result['students']['graduated'] = [
                                'males' => $type->males,
                                'females' => $type->females,
                                'total' => $type->total,
                            ];
                        } elseif ($type->type === 'new') {
                            $result['students']['new'] = [
                                'males' => $type->males,
                                'females' => $type->females,
                                'total' => $type->total,
                            ];
                        }
                    }

                    return $result;
                })->values(),
            ];
        })->values();

        if ($grouped->isEmpty()) {
            return response()->json(['message' => 'No data found'], 404);
        }

        return response()->json([
            'data' => $grouped,
        ]);
    }
}

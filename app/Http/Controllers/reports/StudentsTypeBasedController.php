<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentStatistic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentsTypeBasedController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = Auth::user();
        $year = $request->query('year');
        $shift = $request->query('shift');
        $perPage = $request->query('perPage', 10);

        // STEP 1: Paginate faculties
        $facultyPaginator = DB::table('student_statistics')
            ->join('universities', 'student_statistics.university_id', '=', 'universities.id')
            ->join('faculties', 'student_statistics.faculty_id', '=', 'faculties.id')
            ->select(
                'student_statistics.university_id',
                'universities.name as university_name',
                'student_statistics.faculty_id',
                'faculties.name as faculty_name'
            )
            ->when(!$user->hasRole('admin') && $user->university_id, function ($query) use ($user) {
                return $query->where('student_statistics.university_id', $user->university_id);
            })
            ->where('student_statistics.academic_year', $year)
            ->when($shift && $shift !== 'all', function ($query) use ($shift) {
                return $query->whereRaw('LOWER(student_statistics.shift) = ?', [strtolower(trim($shift))]);
            })
            ->groupBy(
                'student_statistics.university_id',
                'universities.name',
                'student_statistics.faculty_id',
                'faculties.name'
            )
            ->paginate($perPage);

        $facultyIds = $facultyPaginator->pluck('faculty_id')->toArray();

        // STEP 2: Fetch student type stats for faculties
        $typeResults = DB::table('student_statistics')
            ->join('universities', 'student_statistics.university_id', '=', 'universities.id')
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
            )
            ->where('student_statistics.academic_year', $year)
            ->when($shift && $shift !== 'all', function ($query) use ($shift) {
                return $query->whereRaw('LOWER(student_statistics.shift) = ?', [strtolower(trim($shift))]);
            })
            ->whereIn('student_statistics.faculty_id', $facultyIds)
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

        // STEP 3: Build nested structure
        $universities = [];
        $facultiesMap = [];

        foreach ($facultyPaginator as $fac) {
            $uniId = $fac->university_id;
            $facId = $fac->faculty_id;

            // Create university if not exists
            if (!isset($universities[$uniId])) {
                $universities[$uniId] = [
                    'university' => $fac->university_name,
                    'faculties' => []
                ];
            }

            // Create faculty if not exists
            if (!isset($facultiesMap[$facId])) {
                $facultiesMap[$facId] = [
                    'faculty' => $fac->faculty_name,
                    'students' => [
                        'new' => ['males' => 0, 'females' => 0, 'total' => 0],
                        'graduated' => ['males' => 0, 'females' => 0, 'total' => 0],
                        'current' => ['males' => 0, 'females' => 0, 'total' => 0],
                    ]
                ];
                $universities[$uniId]['faculties'][] = &$facultiesMap[$facId];
            }
        }

        // Fill in student type stats
        foreach ($typeResults as $row) {
            if (isset($facultiesMap[$row->faculty_id])) {
                if ($row->type === 'current') {
                    $facultiesMap[$row->faculty_id]['students']['current'] = [
                        'males' => $row->males,
                        'females' => $row->females,
                        'total' => $row->total,
                    ];
                } elseif ($row->type === 'graduated') {
                    $facultiesMap[$row->faculty_id]['students']['graduated'] = [
                        'males' => $row->males,
                        'females' => $row->females,
                        'total' => $row->total,
                    ];
                } elseif ($row->type === 'new') {
                    $facultiesMap[$row->faculty_id]['students']['new'] = [
                        'males' => $row->males,
                        'females' => $row->females,
                        'total' => $row->total,
                    ];
                }
            }
        }

        // Convert to sequential arrays
        $final = array_values(array_map(function ($uni) {
            $uni['faculties'] = array_values($uni['faculties']);
            return $uni;
        }, $universities));

        // STEP 4: Return result
        return response()->json([
            'data' => $final,
            'current_page' => $facultyPaginator->currentPage(),
            'last_page' => $facultyPaginator->lastPage(),
            'per_page' => $facultyPaginator->perPage(),
            'total' => $facultyPaginator->total(),
        ]);
    }
}

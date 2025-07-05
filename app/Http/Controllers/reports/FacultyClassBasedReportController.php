<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\StudentStatistic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacultyClassBasedReportController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole('admin');
        $year = $request->query('year');
        $season = $request->query('season');
        $university = $request->query('university');
        $perPage = $request->query('perPage', 10);

        // STEP 1: Paginate universities
        $universityPaginator = DB::table('student_statistics')
            ->join('universities', 'student_statistics.university_id', '=', 'universities.id')
            ->join('faculties', 'student_statistics.faculty_id', '=', 'faculties.id')
            ->select(
                'student_statistics.university_id',
                'universities.name as university'
            )
            ->where('student_statistics.academic_year', $year)
            ->when(!$isAdmin, function ($query) use ($user) {
                return $query->where('student_statistics.university_id', $user->university_id);
            })
            ->when($season !== 'all', function ($query) use ($season) {
                return $query->where('student_statistics.season', $season);
            })
            ->when($university !== 'all', function ($query) use ($university) {
                return $query->where('universities.id', $university);
            })
            ->groupBy('student_statistics.university_id', 'universities.name')
            ->paginate($perPage);

        $universityIds = $universityPaginator->pluck('university_id')->toArray();

        // STEP 2: Get all faculties for the paginated universities
        $faculties = DB::table('student_statistics')
            ->join('faculties', 'student_statistics.faculty_id', '=', 'faculties.id')
            ->join('universities', 'student_statistics.university_id', '=', 'universities.id')
            ->select(
                'student_statistics.university_id',
                'student_statistics.faculty_id',
                'faculties.name as faculty_name'
            )
            ->where('student_statistics.academic_year', $year)
            ->where('student_statistics.academic_year', $year)
            ->when($season !== 'all', function ($query) use ($season) {
                return $query->where('student_statistics.season', $season);
            })
            ->when($university !== 'all', function ($query) use ($university) {
                return $query->where('universities.id', $university);
            })
            ->whereIn('student_statistics.university_id', $universityIds)
            ->groupBy('student_statistics.university_id', 'student_statistics.faculty_id', 'faculties.name')
            ->get();

        // STEP 3: Get all classroom results
        $classResults = DB::table('student_statistics')
            ->join('universities', 'student_statistics.university_id', '=', 'universities.id')
            ->join('faculties', 'student_statistics.faculty_id', '=', 'faculties.id')
            ->select(
                'student_statistics.university_id',
                'student_statistics.faculty_id',
                'student_statistics.classroom',
                'student_statistics.shift',
                DB::raw('SUM(student_statistics.male_total) as Total_Males'),
                DB::raw('SUM(student_statistics.female_total) as Total_Females'),
                DB::raw('SUM(student_statistics.male_total + student_statistics.female_total) as Total_Students'),
                DB::raw('ROUND((SUM(male_total) / NULLIF(SUM(male_total + female_total), 0)) * 100, 0) as Male_Percentage'),
                DB::raw('ROUND((SUM(female_total) / NULLIF(SUM(male_total + female_total), 0)) * 100, 0) as Female_Percentage')
            )
            ->where('student_statistics.academic_year', $year)
            ->when($season !== 'all', function ($query) use ($season) {
                return $query->where('student_statistics.season', $season);
            })
            ->when($university !== 'all', function ($query) use ($university) {
                return $query->where('universities.id', $university);
            })
            ->whereIn('student_statistics.university_id', $universityIds)
            ->groupBy(
                'student_statistics.university_id',
                'student_statistics.faculty_id',
                'student_statistics.classroom',
                'student_statistics.shift'
            )
            ->get();

        // STEP 4: Get all unique classrooms
        $allClasses = $classResults
            ->pluck('classroom')
            ->filter(fn($classroom) => trim($classroom) !== '')
            ->unique()
            ->sort()
            ->values();

        // STEP 5: Group data
        $grouped = [];

        foreach ($universityPaginator as $university) {
            $grouped[$university->university_id] = [
                'university' => $university->university,
                'faculties' => []
            ];
        }

        foreach ($faculties as $faculty) {
            $grouped[$faculty->university_id]['faculties'][] = [
                'faculty_id' => $faculty->faculty_id, // temp key for matching class data
                'faculty_name' => $faculty->faculty_name,
                'classes' => collect($allClasses)->mapWithKeys(fn($class) => [$class => null])->toArray()
            ];
        }

        foreach ($classResults as $row) {
            $faculties = &$grouped[$row->university_id]['faculties'];

            foreach ($faculties as &$faculty) {
                if ($faculty['faculty_id'] == $row->faculty_id) {
                    $faculty['classes'][$row->classroom] = [
                        'shift' => $row->shift,
                        'Total_Male' => (string) $row->Total_Males,
                        'Total_Female' => (string) $row->Total_Females,
                        'Total_Students' => (string) $row->Total_Students,
                        'Male_Percentage' => (string) $row->Male_Percentage,
                        'Female_Percentage' => (string) $row->Female_Percentage
                    ];
                    break;
                }
            }
        }

        // STEP 6: Remove faculty_id before returning
        foreach ($grouped as &$university) {
            foreach ($university['faculties'] as &$faculty) {
                unset($faculty['faculty_id']);
            }
        }

        // STEP 7: Return final JSON
        return response()->json([
            'data' => array_values($grouped),
            'current_page' => $universityPaginator->currentPage(),
            'last_page' => $universityPaginator->lastPage(),
            'per_page' => $universityPaginator->perPage(),
            'total' => $universityPaginator->total(),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

// app/Http/Controllers/DashboardController.php

use Illuminate\Http\Request;
use App\Models\StudentStatistic;
use App\Models\University;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function summary(Request $request)
    {
        $filters = $request->validate([
            'academic_year' => 'sometimes|integer',
            'season' => 'sometimes|string|in:Spring,Autumn',
            'university_id' => 'sometimes|integer|exists:universities,id',
            'faculty_id' => 'sometimes|integer|exists:faculties,id',
            'department_id' => 'sometimes|integer|exists:departments,id',
            'student_type' => 'sometimes|string',
        ]);

        $statsQuery = StudentStatistic::query();
        $this->applyFilters($statsQuery, $filters);

        $teacherQuery = Teacher::query();
        if (isset($filters['university_id'])) {
            $teacherQuery->where('university_id', $filters['university_id']);
        }

        return response()->json([
            'students' => [
                'total' => $statsQuery->sum(DB::raw('male_total + female_total')),
                'male' => $statsQuery->sum('male_total'),
                'female' => $statsQuery->sum('female_total'),
            ],
            'teachers' => [
                'total' => $teacherQuery->count(),
            ],
            'institutions' => [
                'universities' => isset($filters['university_id']) ? 1 : University::count(),
                'faculties' => isset($filters['faculty_id']) ? 1 : Faculty::count(),
                'departments' => isset($filters['department_id']) ? 1 : Department::count(),
            ],
        ]);
    }

    public function distribution(Request $request)
    {
        $filters = $request->validate([
            'academic_year' => 'sometimes|integer',
            'group_by' => 'required|in:university,faculty,department',
        ]);

        $query = StudentStatistic::query();
        $this->applyFilters($query, $filters);

        $groupField = $filters['group_by'] . '_id';

        $data = $query
            ->select(
                $groupField,
                DB::raw('SUM(male_total + female_total) as total'),
                DB::raw('SUM(male_total) as male'),
                DB::raw('SUM(female_total) as female')
            )
            ->groupBy($groupField)
            ->with($filters['group_by'])
            ->get()
            ->map(function($item) use ($filters) {
                return [
                    'id' => $item->{$filters['group_by'] . '_id'},
                    'name' => $item->{$filters['group_by']}->name,
                    'total' => $item->total,
                    'male' => $item->male,
                    'female' => $item->female,
                ];
            });

        return response()->json($data);
    }

    public function trends(Request $request)
    {
        $filters = $request->validate([
            'academic_year' => 'sometimes|integer',
            'group_by' => 'required|in:year,season,semester',
        ]);

        $query = StudentStatistic::query();
        $this->applyFilters($query, $filters);

        $data = $query
            ->select(
                $filters['group_by'] === 'year' ? 'academic_year' : $filters['group_by'],
                DB::raw('SUM(male_total + female_total) as total')
            )
            ->groupBy($filters['group_by'] === 'year' ? 'academic_year' : $filters['group_by'])
            ->orderBy($filters['group_by'] === 'year' ? 'academic_year' : $filters['group_by'])
            ->get();

        return response()->json($data);
    }

    public function studentTeacherRatio(Request $request)
    {
        $filters = $request->validate([
            'academic_year' => 'sometimes|integer',
            'group_by' => 'required|in:university,faculty',
        ]);

        $studentQuery = StudentStatistic::query();
        $this->applyFilters($studentQuery, $filters);

        $groupField = $filters['group_by'] . '_id';

        $studentCounts = $studentQuery
            ->select(
                $groupField,
                DB::raw('SUM(male_total + female_total) as student_count')
            )
            ->groupBy($groupField)
            ->get()
            ->keyBy($groupField);

        $teacherCounts = Teacher::query()
            ->when(isset($filters['university_id']), function($q) use ($filters, $groupField) {
                $q->where('university_id', $filters['university_id']);
                if ($groupField === 'faculty_id') {
                    $q->groupBy('faculty_id');
                }
            })
            ->select(
                $groupField,
                DB::raw('COUNT(*) as teacher_count')
            )
            ->groupBy($groupField)
            ->get()
            ->keyBy($groupField);

        $result = [];
        foreach ($studentCounts as $id => $studentData) {
            $teachers = $teacherCounts->get($id, (object)['teacher_count' => 0]);
            $result[] = [
                'id' => $id,
                'students' => $studentData->student_count,
                'teachers' => $teachers->teacher_count,
                'ratio' => $teachers->teacher_count > 0 
                    ? round($studentData->student_count / $teachers->teacher_count, 2)
                    : null,
            ];
        }

        return response()->json($result);
    }

    public function filterOptions()
    {
        return response()->json([
            'years' => StudentStatistic::select('academic_year')
                ->distinct()
                ->orderBy('academic_year', 'desc')
                ->pluck('academic_year'),
            'seasons' => ['Spring', 'Autumn'],
            'universities' => University::orderBy('name')->get(['id', 'name']),
            'faculties' => Faculty::orderBy('name')->get(['id', 'name']),
            'departments' => Department::orderBy('name')->get(['id', 'name']),
        ]);
    }

    private function applyFilters($query, $filters)
    {
        if (isset($filters['academic_year'])) {
            $query->where('academic_year', $filters['academic_year']);
        }
        
        if (isset($filters['season'])) {
            $query->where('season', $filters['season']);
        }
        
        if (isset($filters['university_id'])) {
            $query->where('university_id', $filters['university_id']);
        }
        
        if (isset($filters['faculty_id'])) {
            $query->where('faculty_id', $filters['faculty_id']);
        }
        
        if (isset($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }
        
        if (isset($filters['student_type'])) {
            $query->where('student_type', $filters['student_type']);
        }
        
        return $query;
    }
}
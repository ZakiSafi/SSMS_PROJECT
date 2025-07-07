<?php

namespace App\Services;

use App\Models\StudentStatistic;
use App\Models\University;
use App\Models\ActivityLog;
use App\Models\Log;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getSummaryData(array $filters = [])
    {
        $studentQuery = $this->applyFilters(StudentStatistic::query(), $filters);
        $universityQuery = $this->applyUniversityFilters(University::query(), $filters);

        // Sum of male and female
        $totalStudents = (clone $studentQuery)
            ->where('student_type', '!=', 'graduated')
            ->selectRaw('SUM(male_total + female_total) as total')
            ->value('total') ?? 0;

        // New students total (male + female)
        $newStudents = (clone $studentQuery)
            ->where('student_type', 'new')
            ->selectRaw('SUM(male_total + female_total) as total')
            ->value('total') ?? 0;

        // Graduated students total (male + female)
        $graduatedStudents = (clone $studentQuery)
            ->where('student_type', 'graduated')
            ->selectRaw('SUM(male_total + female_total) as total')
            ->value('total') ?? 0;

        $publicUniCount = (clone $universityQuery)->where('type', 'public')->count();
        $privateUniCount = (clone $universityQuery)->where('type', 'private')->count();

        return [
            'total_students' => $totalStudents,
            'new_students' => $newStudents,
            'graduated_students' => $graduatedStudents,
            'universities_count' => [
                'public' => $publicUniCount,
                'private' => $privateUniCount,
                'total' => $publicUniCount + $privateUniCount
            ],
            'student_teacher_ratio' => $this->calculateStudentTeacherRatio($filters),
        ];
    }


    public function getTrendData(array $filters = [])
    {
        $query = StudentStatistic::query()
            ->where('student_type', '!=', 'graduated') // Exclude graduated students
            ->select(
                'academic_year as year',
                DB::raw('SUM(male_total + female_total) as total'),
                DB::raw('SUM(male_total) as male'),
                DB::raw('SUM(female_total) as female')
            );

        if (!empty($filters['group_by']) && $filters['group_by'] === 'season') {
            $query->addSelect('season');
        }

        // Apply season filter if specifically passed
        if (!empty($filters['season']) && $filters['season'] !== 'all') {
            $query->where('season', $filters['season']);
        }

        $query = $this->applyFilters($query, $filters);

        // Handle time_range (last N years)
        if (!empty($filters['time_range'])) {
            $years = (int) str_replace('years', '', $filters['time_range']);
            $latestYear = StudentStatistic::max('academic_year');
            if ($latestYear) {
                $query->where('academic_year', '>=', $latestYear - $years + 1);
            }
        }

        // Dynamic grouping
        if (!empty($filters['group_by']) && $filters['group_by'] === 'season') {
            $query->groupBy('academic_year', 'season')
                ->orderBy('academic_year')
                ->orderBy('season');
        } else {
            $query->groupBy('academic_year')
                ->orderBy('academic_year');
        }

        return $query->get();
    }

    // Get gender distribution statistics

    public function getGenderDistribution(array $filters = [])
    {
        $query = $this->applyFilters(
            StudentStatistic::query()
                ->where('student_type', '!=', 'graduated') // ✅ Exclude graduated students
                ->selectRaw('SUM(male_total) as male, SUM(female_total) as female'),
            $filters
        );

        $result = $query->first();

        $male = $result->male ?? 0;
        $female = $result->female ?? 0;
        $total = $male + $female;

        return [
            'data' => [
                ['gender' => 'male', 'count' => $male],
                ['gender' => 'female', 'count' => $female],
            ],
            'percentages' => [
                'male' => $total > 0 ? round(($male / $total) * 100, 2) : 0,
                'female' => $total > 0 ? round(($female / $total) * 100, 2) : 0,
            ]
        ];
    }


    // Get faculty breakdown statistics
    public function getFacultyBreakdown(array $filters = [])
    {
        $level = $filters['breakdown_level'] ?? 'faculty';
        unset($filters['breakdown_level']);

        $query = $this->applyFilters(
            StudentStatistic::query()
                ->with([$level => function ($q) {
                    $q->select('id', 'name');
                }]),
            $filters
        );

        // Exclude graduated students
        $query->where('student_type', '!=', 'graduated');

        // Use SUM of male + female totals instead of counting rows
        if ($level === 'faculty') {
            $query->select(
                'faculty_id',
                DB::raw('SUM(male_total + female_total) as total_students')
            )->groupBy('faculty_id');
        } else {
            $query->select(
                'department_id',
                DB::raw('SUM(male_total + female_total) as total_students')
            )->groupBy('department_id');
        }

        // Order results by total descending
        $query->orderByDesc('total_students');

        return $query->get()->map(function ($item) use ($level) {
            return [
                'id' => $item->{$level . '_id'},
                'name' => optional($item->$level)->name ?? 'Unknown',
                'total_students' => (int) $item->total_students,
            ];
        });
    }

    public function getUniversityComparison(array $filters = [])
    {
        $studentStats = StudentStatistic::query()
            ->where('student_type', '!=', 'graduated')
            ->select(
                'university_id',
                DB::raw('SUM(male_total + female_total) as students_count')
            )
            ->groupBy('university_id');

        $this->applyFilters($studentStats, $filters);

        $query = University::query()
            ->leftJoinSub($studentStats, 'student_stats', function ($join) {
                $join->on('universities.id', '=', 'student_stats.university_id');
            })
            ->join('provinces', 'universities.province_id', '=', 'provinces.id');

        // Determine grouping based on compare_by parameter
        switch ($filters['compare_by']) {
            case 'type':
                $query->select([
                    'universities.type',
                    DB::raw('COUNT(*) as university_count'),
                    DB::raw('SUM(COALESCE(student_stats.students_count, 0)) as total_students')
                ])
                    ->groupBy('universities.type');
                break;

            case 'province':
                $query->select([
                    'provinces.id as province_id',
                    'provinces.name as province_name',
                    DB::raw('COUNT(*) as university_count'),
                    DB::raw('SUM(COALESCE(student_stats.students_count, 0)) as total_students')
                ])
                    ->groupBy('provinces.id', 'provinces.name');
                break;

            case 'both':
                $query->select([
                    'universities.type',
                    'provinces.id as province_id',
                    'provinces.name as province_name',
                    DB::raw('COUNT(*) as university_count'),
                    DB::raw('SUM(COALESCE(student_stats.students_count, 0)) as total_students')
                ])
                    ->groupBy('universities.type', 'provinces.id', 'provinces.name');
                break;
        }

        // Apply province filter if specified
        if (!empty($filters['province_id'])) {
            $query->where('provinces.id', $filters['province_id']);
        }

        return $query->get();
    }

    public function getRecentActivity(array $filters = [])
    {
        $query = DB::table('logs')
            ->select([
                'logs.id',
                'logs.action_type',
                'logs.action_description',
                'logs.table_name',
                'logs.record_id',
                'logs.created_at',
                'users.name as user_name',
                'users.email as user_email',
                'universities.name as university_name',
                'universities.type as university_type'
            ])
            ->leftJoin('users', 'logs.user_id', '=', 'users.id')
            ->leftJoin('universities', 'logs.university_id', '=', 'universities.id')
            ->orderBy('logs.created_at', 'desc')
            ->limit($filters['limit'] ?? 10);

        // Apply filters
        if (!empty($filters['user_id'])) {
            $query->where('logs.user_id', $filters['user_id']);
        }

        if (!empty($filters['university_id'])) {
            $query->where('logs.university_id', $filters['university_id']);
        }

        if (!empty($filters['action_type'])) {
            $query->where('logs.action_type', $filters['action_type']);
        }

        return $query->get()->map(function ($log) {
            // Add a human-readable description if none exists
            if (empty($log->action_description)) {
                $log->action_description = $this->generateActionDescription($log);
            }
            return $log;
        });
    }

    protected function generateActionDescription($log)
    {
        $actionMap = [
            'create' => 'Created new record in',
            'update' => 'Updated record in',
            'delete' => 'Deleted record from',
            'login' => 'Logged in to system',
            'logout' => 'Logged out from system',
        ];

        $base = $actionMap[$log->action_type] ?? 'Performed action on';

        if ($log->table_name) {
            return sprintf('%s %s (ID: %d)', $base, $log->table_name, $log->record_id);
        }

        return $base;
    }

    protected function applyFilters($query, $filters)
    {
        if (!empty($filters['year'])) {
            $query->where('academic_year', $filters['year']);
        }

        if (!empty($filters['season'])) {
            $query->where('season', $filters['season']);
        }

        if (!empty($filters['university_type'])) {
            $query->whereHas('university', function ($q) use ($filters) {
                $q->where('type', $filters['university_type']);
            });
        }

        if (!empty($filters['province_id'])) {
            $query->whereHas('university', function ($q) use ($filters) {
                $q->where('province_id', $filters['province_id']);
            });
        }

        if (!empty($filters['university_id'])) {
            $query->where('university_id', $filters['university_id']);
        }

        if (!empty($filters['faculty_id'])) {
            $query->where('faculty_id', $filters['faculty_id']);
        }

        if (!empty($filters['shift'])) {
            $query->where('shift', $filters['shift']);
        }

        return $query;
    }

    protected function applyUniversityFilters($query, $filters)
    {
        if (!empty($filters['province_id'])) {
            $query->where('province_id', $filters['province_id']);
        }

        if (!empty($filters['university_type']) && $filters['university_type'] !== 'all') {
            $query->where('type', $filters['university_type']);
        }

        // ✅ Filter by year ONLY if querying Teacher model
        if (!empty($filters['year']) && $query->getModel() instanceof \App\Models\Teacher) {
            $query->where('academic_year', $filters['year']);
        }

        return $query;
    }


    protected function calculateStudentTeacherRatio($filters)
    {
        // Sum actual students (male + female), excluding graduated if needed
        $studentCount = $this->applyFilters(StudentStatistic::query(), $filters)
            ->where('student_type', '!=', 'graduated') // optional: exclude grads
            ->selectRaw('SUM(male_total + female_total) as total')
            ->value('total') ?? 0;

        $teacherCount = $this->applyUniversityFilters(Teacher::query(), $filters)
            ->sum('total_teachers');

        return $teacherCount > 0 ? round($studentCount / $teacherCount, 2) : 0;
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentStatistic;
use App\Models\University;
use App\Models\Teacher;
use App\Models\Faculty;
use App\Services\DashboardService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Get summary statistics for dashboard
     */
    public function summary(Request $request)
    {
        $validated = $request->validate([
            'year' => 'nullable|integer',
            'season' => 'nullable|in:spring,autumn',
            'university_type' => 'nullable|in:public,private,all',
            'province_id' => 'nullable|exists:provinces,id',
            'university_id' => 'nullable|exists:universities,id',
            'faculty_id' => 'nullable|exists:faculties,id',
            'shift' => 'nullable|in:day,night',
        ]);

        $filters = array_filter($validated, function ($value) {
            return $value !== null && $value !== 'all';
        });

        return response()->json([
            'success' => true,
            'data' => $this->dashboardService->getSummaryData($filters)
        ]);
    }

    /**
     * Get student trends over time
     */
    public function trends(Request $request)
    {
        $validated = $request->validate([
            'university_type' => 'nullable|in:public,private,all',
            'province_id' => 'nullable|exists:provinces,id',
            'time_range' => 'nullable|in:5years,10years,all',
            'group_by' => 'nullable|in:year,season',
            'season' => 'nullable|in:spring,autumn,all', // optional if you added this
            'year' => 'nullable|integer|min:1300|max:1600',

        ]);

        $filters = array_filter($validated, function ($value) {
            return $value !== null && $value !== 'all';
        });

        return response()->json([
            'success' => true,
            'data' => $this->dashboardService->getTrendData($filters)
        ]);
    }

    /**
     * Get gender distribution statistics
     */
    public function genderDistribution(Request $request)
    {
        $validated = $request->validate([
            'year' => 'nullable|integer',
            'season' => 'nullable|in:spring,autumn',
            'university_type' => 'nullable|in:public,private,all',
            'province_id' => 'nullable|exists:provinces,id',
        ]);

        $filters = array_filter($validated, function ($value) {
            return $value !== null && $value !== 'all';
        });

        return response()->json([
            'success' => true,
            'data' => $this->dashboardService->getGenderDistribution($filters)
        ]);
    }

    /**
     * Get faculty/department breakdown
     */
    public function facultyBreakdown(Request $request)
    {
        $validated = $request->validate([
            'year' => 'nullable|integer',
            'season' => 'nullable|in:spring,autumn',
            'university_type' => 'nullable|in:public,private,all',
            'province_id' => 'nullable|exists:provinces,id',
            'university_id' => 'nullable|exists:universities,id',
            'breakdown_level' => 'nullable|in:faculty,department',
        ]);

        $filters = array_filter($validated, function ($value) {
            return $value !== null && $value !== 'all';
        });

        return response()->json([
            'success' => true,
            'data' => $this->dashboardService->getFacultyBreakdown($filters)
        ]);
    }

    /**
     * Get university comparison data
     */
    public function universityComparison(Request $request)
    {
        $validated = $request->validate([
            'year' => 'nullable|integer',
            'season' => 'nullable|in:spring,autumn',
            'province_id' => 'nullable|exists:provinces,id',
            'compare_by' => 'required|in:type,province,both',
        ]);

        return response()->json([
            'success' => true,
            'data' => $this->dashboardService->getUniversityComparison($validated)
        ]);
    }

    /**
     * Get recent activity for dashboard
     */
    public function recentActivity(Request $request)
    {
        $validated = $request->validate([
            'limit' => 'nullable|integer|max:50',
            'user_id' => 'nullable|exists:users,id',
        ]);

        return response()->json([
            'success' => true,
            'data' => $this->dashboardService->getRecentActivity($validated)
        ]);
    }
}

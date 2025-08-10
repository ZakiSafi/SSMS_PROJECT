<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;
use App\Models\StudentStatistic;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StudentStatisticRequest;
use App\Http\Resources\StudentStatisticResource;

class StudentStatisticController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:student_statistic.view')->only(['index', 'show']);
        $this->middleware('permission:student_statistic.edit')->only(['edit', 'update']);
        $this->middleware('permission:student_statistic.create')->only(['create', 'store']);
        $this->middleware('permission:student_statistic.delete')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    private $model = StudentStatistic::class;
    public function index(Request $request)
    {
        $query = StudentStatistic::with(['university', 'faculty', 'department']);

        // Apply filters
        if ($request->filled('province')) {
            $query->whereHas('university', function ($q) use ($request) {
                $q->where('province_id', $request->province);
            });
        }

        if ($request->filled('university_type')) {
            $query->whereHas('university', function ($q) use ($request) {
                $q->where('type', $request->university_type);
            });
        }

        if ($request->filled('university')) {
            $query->where('university_id', $request->university);
        }

        if ($request->filled('faculty')) {
            $query->where('faculty_id', $request->faculty);
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        if ($request->filled('student_type')) {
            $query->where('student_type', $request->student_type);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('classroom', 'like', "%$search%")
                    ->orWhere('shift', 'like', "%$search%")
                    ->orWhere('season', 'like', "%$search%")
                    ->orWhereHas('university', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('faculty', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('department', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    });
            });
        }

        // Get filter options for active filters
        $filterOptions = [];
        if ($request->filled('province') || $request->filled('university_type')) {
            $filterOptions['universities'] = $query->clone()
                ->select('university_id')
                ->with('university:id,name')
                ->get()
                ->pluck('university')
                ->unique()
                ->values();
        }

        if ($request->filled('university') || $request->filled('faculty')) {
            $filterOptions['faculties'] = $query->clone()
                ->select('faculty_id')
                ->with('faculty:id,name')
                ->get()
                ->pluck('faculty')
                ->unique()
                ->values();
        }

        if ($request->filled('faculty') || $request->filled('department')) {
            $filterOptions['departments'] = $query->clone()
                ->select('department_id')
                ->with('department:id,name')
                ->get()
                ->pluck('department')
                ->unique()
                ->values();
        }

        // Pagination
        $perPage = $request->get('perPage', 10);
        if ($perPage == -1) {
            $perPage = $query->count();
        }

        $studentStatistics = $query->paginate($perPage);

        return response()->json([
            'data' => StudentStatisticResource::collection($studentStatistics),
            'filter_options' => $filterOptions,
            'meta' => [
                'total' => $studentStatistics->total(),
                'per_page' => $studentStatistics->perPage(),
                'current_page' => $studentStatistics->currentPage(),
            ]
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StudentStatisticRequest $request)
    {
        return $this->storeRecord($request, $this->model);
    }

    /**
     * Display the specified resource.
     */
    public function show(StudentStatistic $studentStatistic)
    {
        return new StudentStatisticResource($studentStatistic);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StudentStatisticRequest $request, StudentStatistic $studentStatistic)
    {
        return $this->updateRecord($request, $studentStatistic);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentStatistic $studentStatistic)
    {
        return $this->deleteRecord($studentStatistic);
    }
}

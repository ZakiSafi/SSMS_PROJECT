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
    public function index()
    {
        // Start query manually to apply relationship filters
        $query = $this->model::with(['university', 'faculty', 'department']);

        // Apply relationship-based filters
        if (request()->filled('province')) {
            $query->whereHas('university', function ($q) {
                $q->where('province_id', request('province'));
            });
        }

        if (request()->filled('university_type')) {
            $query->whereHas('university', function ($q) {
                $q->where('type', request('university_type'));
            });
        }

        // Set the query temporarily on the model so listRecord works as is
        $this->model = $query->getModel();

        // Now use the existing listRecord to apply basic filters + pagination
        $studentStatistics = $this->listRecord(
            request(),
            get_class($query->getModel()),
            [
                'university_id',
                'faculty_id',
                'department_id',
                'academic_year',
                'student_type',
            ],
            ['university', 'faculty', 'department']
        );

        return StudentStatisticResource::collection($studentStatistics);
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

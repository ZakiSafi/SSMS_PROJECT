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
        $studentStatistics = $this->listRecord(request(), $this->model, [ ], [ 'university', 'faculty', 'department']);
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

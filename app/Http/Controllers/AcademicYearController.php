<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcademicYearRequest;
use App\Http\Resources\AcademicYearResource;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $model= AcademicYear::class;
    public function index(Request $request)
    {
        $academicYears = $this->listRecord($request, $this->model);
        return AcademicYearResource::collection($academicYears);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AcademicYearRequest $request)
    {
        return $this->storeRecord($request, $this->model);
    }

    /**
     * Display the specified resource.
     */
    public function show(AcademicYear $academicYear)
    {
        return new AcademicYearResource($this->showRecord($academicYear));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AcademicYearRequest $request, AcademicYear $academicYear )
    {
        return $this->updateRecord($request, $academicYear);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicYear $academicYear)
    {
        return $this->deleteRecord($academicYear);
    }
}

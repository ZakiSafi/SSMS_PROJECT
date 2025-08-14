<?php

namespace App\Http\Controllers;

use App\Http\Requests\FacultyRequest;
use App\Http\Resources\FacultyResource;
use App\Models\Faculty;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class FacultyController extends Controller
{
    public function __construct()
    {


        // Apply permission middleware with explicit guard
        $this->middleware('permission:faculties.view', ['guard' => 'api'])->only(['index', 'show']);
        $this->middleware('permission:faculties.edit', ['guard' => 'api'])->only(['edit', 'update']);
        $this->middleware('permission:faculties.create', ['guard' => 'api'])->only(['create', 'store']);
        $this->middleware('permission:faculties.delete', ['guard' => 'api'])->only('destroy');
    }

    private $model = Faculty::class;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $faculty = $this->listRecord(
            $request,
            $this->model,
            ['name'],
            'university',
            
        );

        return FacultyResource::collection($faculty);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(FacultyRequest $request)
    {
        return $this->storeRecord($request, $this->model);
    }

    /**
     * Display the specified resource.
     */
    public function show(Faculty $faculty)
    {
        return new FacultyResource($this->showRecord($faculty));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FacultyRequest $request, Faculty $faculty)
    {
        return $this->updateRecord($request, $faculty);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faculty $faculty)
    {
        return $this->deleteRecord($faculty);
    }
}

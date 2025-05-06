<?php

namespace App\Http\Controllers;

use App\Http\Requests\FacultyRequest;
use App\Http\Resources\FacultyResource;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class FacultyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $model = Faculty::class;
    public function index(Request $request)
    {
        $faculty = $this->listRecord($request, $this->model);
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

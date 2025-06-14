<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeacherRequest;
use App\Http\Resources\TeacherResource;
use App\Models\Teacher;
use App\Models\University;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $model = Teacher::class;
    public function index( Request $request)
    {
        $teachers = $this->listRecord($request, $this->model, ['university_id', 'academic_year'], ['university']);
        return TeacherResource::collection($teachers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TeacherRequest $request)
    {
        return $this->storeRecord($request, $this->model);

    }

    /**
     * Display the specified resource.
     */
    public function show(Teacher $teacher)
    {
        return new TeacherResource($this->showRecord($teacher));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TeacherRequest $request, Teacher $teacher)
    {
        return $this->updateRecord($request, $teacher);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teacher $teacher)
    {
        return $this->deleteRecord($teacher);
    }
}

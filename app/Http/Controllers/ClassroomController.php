<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClassroomRequest;
use App\Http\Resources\ClassroomResource;
use App\Models\Classroom;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    private $model = Classroom::class;
    public function index(Request $request)
    {
        $classroom = $this->listRecord($request, $this->model);
        return ClassroomResource::collection($classroom);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ClassroomRequest $request)
    {
        return $this->storeRecord($request, $this->model);
    }

    /**
     * Display the specified resource.
     */
    public function show(classroom $classroom)
    {
        return new ClassroomResource($this->showRecord($classroom));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ClassroomRequest $request, Classroom $classRoom)
    {
        return $this->updateRecord($request, $classRoom);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classroom $classRoom)
    {
        return $this->deleteRecord($classRoom);
    }
}

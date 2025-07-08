<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
        public function __construct()
    {
        $this->middleware('permission:departments.view')->only(['index', 'show']);
        $this->middleware('permission:departments.edit')->only(['edit', 'update']);
        $this->middleware('permission:departments.create')->only(['create', 'store']);
        $this->middleware('permission:departments.delete')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    private $model = Department::class;
    public function index(Request $reuest)
    {
        $departments = $this->listRecord($reuest, $this->model,['name']);
        return DepartmentResource::collection($departments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DepartmentRequest $request)
    {
        return $this->storeRecord($request, $this->model);
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        return new DepartmentResource($this->showRecord($department));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DepartmentRequest $request, Department $department)
    {
        return $this->updateRecord($request, $department);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        return $this->deleteRecord($department);
    }
}

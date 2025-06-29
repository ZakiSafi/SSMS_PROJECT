<?php

namespace App\Http\Controllers;

use App\Http\Requests\RollPermissionRequest;
use Illuminate\Http\Request;
use App\Models\RollPermission;
use App\Http\Resources\RollPermissionResource;
use App\Models\Permission;
use Faker\Provider\ar_EG\Person;

class RollPermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $model = Permission::class;
    public function index(Request $request)
    {
        $rollPermission = $this->listRecord($request, Permission::class);
        return RollPermissionResource::collection($rollPermission);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RollPermissionRequest $request)
    {
        return $this->storeRecord($request, $this->model);
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $rollPermission)
    {
        return new RollPermissionResource($this->showRecord($rollPermission));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RollPermissionRequest $request,Permission $rollPermission)
    {
        return $this->updateRecord($request, $rollPermission);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $rollPermission)
    {
        return $this->deleteRecord($rollPermission);
    }
}

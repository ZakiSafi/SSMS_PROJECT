<?php

namespace App\Http\Controllers;

use App\Http\Requests\RollPermissionRequest;
use Illuminate\Http\Request;
use App\Models\RollPermission;
use App\Http\Resources\RollPermissionResource;

class RollPermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $model = RollPermission::class;
    public function index(Request $request)
    {
        $rollPermission = $this->listRecord($request, RollPermission::class);
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
    public function show(RollPermission $rollPermission)
    {
        return new RollPermissionResource($this->showRecord($rollPermission));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RollPermissionRequest $request,RollPermission $rollPermission)
    {
        return $this->updateRecord($request, $rollPermission);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RollPermission $rollPermission)
    {
        return $this->deleteRecord($rollPermission);
    }
}

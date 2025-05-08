<?php

namespace App\Http\Controllers;

use App\Http\Requests\UniversityRequest;
use App\Http\Resources\UniversityResource;
use App\Models\University;
use Illuminate\Http\Request;

class UniversityController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    private $model = University::class;

    public function index(Request $request)
    {
        $university =  $this->listRecord($request, $this->model, $with = ['provinces']);
        return UniversityResource::collection($university);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UniversityRequest $request)
    {
        return $this->storeRecord($request, $this->model);
    }

    /**
     * Display the specified resource.
     */
    public function show(University $universities)
    {
        $university = $this->showRecord($universities);
        return new UniversityResource($university);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UniversityRequest $request, University $university)
    {
        return $this->updateRecord($request, $university);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(University $universities)
    {
        $university = University::findOrFail($universities->id);
        return $this->deleteRecord($university);
    }
}

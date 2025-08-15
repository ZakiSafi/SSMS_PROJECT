<?php

namespace App\Http\Controllers;

use App\Http\Requests\UniversityRequest;
use App\Http\Resources\UniversityResource;
use App\Models\Faculty;
use App\Models\University;
use Illuminate\Http\Request;

class UniversityController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:university.view')->only(['index', 'show']);
        $this->middleware('permission:university.edit')->only(['edit', 'update']);
        $this->middleware('permission:university.create')->only(['create', 'store']);
        $this->middleware('permission:university.delete')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */

    private $model = University::class;

    public function index(Request $request)
    {
        $university =  $this->listRecord($request, $this->model, ['name'], ['province']);
        return UniversityResource::collection($university);
    }


    // show a university with its faculties

    public function show(University $university)
    {
        return new UniversityResource($university->load('faculties'));
    }


    /**
     * Store a newly created resource in storage. 
     */
    public function store(UniversityRequest $request)
    {
        $university = University::create($request->only(['name', 'type', 'province_id']));

        if ($request->filled('faculty_ids')) {
            $university->faculties()->attach($request->faculty_ids);
        }

        return response()->json([
            'message' => 'University created successfully',
            'data' => $university->load('faculties')
        ]);
    }


    /**
     * Display the specified resource.
     */


    /**
     * Update the specified resource in storage.
     */
    public function update(UniversityRequest $request, University $university)
    {
        // Step 1: Update university's own fields
        $university->update($request->only(['name', 'type', 'province_id']));

        // Step 2: Sync faculties if provided
        if ($request->filled('faculty_ids')) {
            // Attach/detach using sync
            $university->faculties()->sync($request->faculty_ids);
        } else {
            // If no faculty_ids provided, detach all
            $university->faculties()->detach();
        }

        return response()->json([
            'message' => 'University updated successfully',
            'university' => $university->load('faculties'),
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(University $university)
    {
        return $this->deleteRecord($university);
    }
}

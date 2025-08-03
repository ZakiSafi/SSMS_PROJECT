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

    /**
     * Store a newly created resource in storage. 
     */
    public function store(UniversityRequest $request)
    {
        // Step 1: Create the university
        $university = University::create($request->only(['name', 'type', 'province_id']));

        // Step 2: Duplicate each selected general faculty and assign it to the university
        if ($request->filled('faculty_ids')) {
            $generalFaculties = Faculty::whereIn('id', $request->faculty_ids)
                ->whereNull('university_id') // Ensure only general ones are selected
                ->get();


            foreach ($generalFaculties as $generalFaculty) {
                $exists = Faculty::where('name', $generalFaculty->name)
                    ->where('university_id', $university->id)
                    ->exists();

                if (!$exists) {
                    Faculty::create([
                        'name' => $generalFaculty->name,
                        'university_id' => $university->id,
                    ]);
                }
            }
        }

        return response()->json([
            'message' => 'University created successfully',
            'university' => $university->load('faculties'),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(University $university)
    {

        return new UniversityResource($this->showRecord($university));
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
    public function destroy(University $university)
    {
        return $this->deleteRecord($university);
    }
}

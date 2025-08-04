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


    /**
     * Update the specified resource in storage.
     */
    public function update(UniversityRequest $request, University $university)
    {
        // Step 1: Update the university's own fields
        $university->update($request->only(['name', 'type', 'province_id']));

        // Step 2: Handle faculties updates if faculty_ids is provided
        if ($request->filled('faculty_ids')) {
            // Get general faculties selected (without university_id)
            $generalFaculties = Faculty::whereIn('id', $request->faculty_ids)
                ->whereNull('university_id')
                ->get();

            // Get current faculties assigned to this university
            $currentFaculties = $university->faculties()->pluck('id')->toArray();

            // Prepare array to hold new faculty IDs after duplication
            $newFacultyIds = [];

            foreach ($generalFaculties as $generalFaculty) {
                // Check if this faculty is already assigned (by name)
                $faculty = Faculty::where('name', $generalFaculty->name)
                    ->where('university_id', $university->id)
                    ->first();

                if (!$faculty) {
                    // Duplicate faculty for this university
                    $faculty = Faculty::create([
                        'name' => $generalFaculty->name,
                        'university_id' => $university->id,
                    ]);
                }
                $newFacultyIds[] = $faculty->id;
            }

            // Optionally, remove faculties no longer in the updated list
            // You might want to delete or detach faculties that are no longer selected
            $facultiesToRemove = array_diff($currentFaculties, $newFacultyIds);

            if (!empty($facultiesToRemove)) {
                Faculty::whereIn('id', $facultiesToRemove)->delete();
                // Or detach if you have pivot table, here you delete since faculties belong to university
            }
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

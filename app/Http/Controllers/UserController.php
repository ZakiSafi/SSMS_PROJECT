<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
         public function __construct()
    {
        $this->middleware('permission:user.view')->only(['index', 'show']);
        $this->middleware('permission:user.edit')->only(['edit', 'update']);
        $this->middleware('permission:user.create')->only(['create', 'store']);
        $this->middleware('permission:user.delete')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    private $model = User::class;
    public function index(Request $request)
    {
        $users = $this->listRecord($request, $this->model, ['name', 'email']);
        return UserResource::collection($users);
    }


    /**
     * Store a newly created resource in storage.
     */
   public function store(UserRequest $request)
{
    DB::beginTransaction();
    try {
        $validated = $request->validated();
        
        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'university_id' => $validated['university_id'] ?? null,
        ]);

        // Assign role (using either role ID or name)
        if (isset($validated['role_id'])) {
            $role = Role::findById($validated['role_id'], 'api');
            $user->assignRole($role);
        }

        DB::commit();

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user->load('roles')
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'User creation failed',
            'error' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return new UserResource($this->showRecord($user));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        return $this->updateRecord($request, $user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( User $user)
    {
        return $this->deleteRecord($user);
    }
}

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
    /**
     * Display a listing of the resource.
     */
    private $model = User::class;

    public function index(Request $request)
    {
        try {
            $users = $this->listRecord($request, $this->model, ['name', 'email']);

            return $this->successResponse(
                UserResource::collection($users),
                'Users retrieved successfully',
                200,
                [
                    'total' => $users->total(),
                    'per_page' => $users->perPage(),
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'from' => $users->firstItem(),
                    'to' => $users->lastItem()
                ]
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve users',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'university_id' => $validated['university_id'] ?? null,
            ]);

            if (isset($validated['role_id'])) {
                $role = Role::findById($validated['role_id'], 'api');
                $user->assignRole($role);
            }

            DB::commit();

            return $this->successResponse(
                new UserResource($user->load('roles')),
                'User created successfully',
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse(
                'User creation failed',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        try {
            return $this->successResponse(
                new UserResource($this->showRecord($user)),
                'User retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve user',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        DB::beginTransaction();
        try {
            $user = $this->updateRecord($request, $user);
            $validated = $request->validated();

            if (isset($validated['role_id'])) {
                $role = Role::findOrFail($validated['role_id']);
                $user->syncRoles([$role]);
            }

            DB::commit();

            return $this->successResponse(
                new UserResource($user->load('roles')),
                'User updated successfully'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse(
                'User update failed',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            $this->deleteRecord($user);
            return $this->successResponse(
                null,
                'User deleted successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'User deletion failed',
                500,
                $e->getMessage()
            );
        }
    }

    public function me(Request $request)
    {
        try {
            return $this->successResponse(
                new UserResource($request->user()),
                'User profile retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve user profile',
                500,
                $e->getMessage()
            );
        }
    }
}

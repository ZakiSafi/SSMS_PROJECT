<?php

namespace App\Http\Controllers;

use App\Http\Requests\RollPermissionRequest;
use App\Http\Requests\RollRequest;
use App\Http\Resources\RollPermissionResource;
use App\Http\Resources\RollResource;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class RoleController extends Controller
{
     private $model = Role::class;
    public function index(Request $request)
    {
        $roles = $this->listRecord($request, $this->model);
        return RollResource::collection($roles);
    }
     
    // Showig a single role
     public function show($id)
    {
        $role = Role::where('id', $id)->first();
        return new RollPermissionResource($role);
    }

    // Updating Roll Permissions
    public function update(RollPermissionRequest $request, $id)
{
    $validated = $request->validated();
    
    // Start a database transaction
    DB::beginTransaction();
    
    try {
        // Find the role or fail
        $role = Role::findOrFail($id);
        
        // Update role attributes
        $role->name = $validated['name'];
        $role->description = $validated['description'];
        $role->save();
        
        // Prepare permissions (assuming permissionsArray is an array of permission names)
        $permissionsArray = $validated['permissions'] ?? [];
        
        // Get or create all permissions at once (more efficient than in a loop)
        $permissions = collect($permissionsArray)->map(function ($permissionName) {
            return Permission::firstOrCreate(
                ['name' => $permissionName],
                ['guard_name' => 'web'] // or config('auth.defaults.guard')
            );
        });
        
        // Sync permissions
        $role->syncPermissions($permissions);
        
        // Commit the transaction
        DB::commit();
        
        return new RollPermissionResource($role->fresh()); // fresh() to reload with permissions
        
    } catch (\Exception $e) {
        DB::rollBack();
        // Handle the error appropriately
        return response()->json(['message' => 'Update failed'], 500);
    }
}

// Storing a new role with permissions
    public function store(RollRequest $request)
    {
        $validated = $request->validated();

        // Create role with guard_name (use 'web' or 'api' based on your auth system)
        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web', // or 'api' if using Sanctum/API only
            'description' => $validated['description'] ?? null
        ]);

        // Process permissions
        $permissionIds = [];
        foreach ($validated['permissions'] ?? [] as $perm) {
            $permission = Permission::firstOrCreate(
                [
                    'name' => $perm['name'],
                    'guard_name' => 'web' // Must match role's guard_name
                ],
                [
                    'description' => $perm['description'] ?? null
                ]
            );
            $permissionIds[] = $permission->id;
        }

        $role->syncPermissions($permissionIds);

        return response()->json([
            'message' => 'Role created successfully',
            'role' => $role->load('permissions')
        ]);
    }

    // Deleting a role
    public function destroy(Role $role)
    {
        $role->delete();
        return response()->json(['message' => 'Role deleted successfully']);   
        
    }
}

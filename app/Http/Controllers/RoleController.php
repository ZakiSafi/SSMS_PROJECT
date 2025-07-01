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
        return new RollResource($role);
    }

    // Updating Roll Permissions
    public function update(RollRequest $request, $id)
    {
        $validated= $request->validated();
        $permissionsArray = $validated['permissions'];
    
        // Find the role by ID
        $role = Role::where('id', $id)->first();
        $role->name = $validated['name'];
        $role->description = $validated['description'];
    
        // Find or create each permission and sync them with the role
        
        foreach ($permissionsArray as $permission) {
             Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
            
        }
    
        // Sync the permissions with the role
        $role->syncPermissions($permissionsArray);
    
        // Save the updated role details
        $role->save();
    
        return new RollResource($role);
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

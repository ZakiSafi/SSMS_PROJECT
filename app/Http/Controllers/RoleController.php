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

    // Create role with guard_name (must match with permissions)
    $role = Role::create([
        'name' => $validated['name'],
        'guard_name' => 'web',
        'description' => $validated['description'] ?? null
    ]);

    // Process and assign permissions
    $permissions = [];
    foreach ($validated['permissions'] ?? [] as $perm) {
        $permission = Permission::firstOrCreate(
            [
                'name' => $perm['name'],
                'guard_name' => 'web'
            ],
            [
                'description' => $perm['description'] ?? null
            ]
        );
        $permissions[] = $permission;
    }

    // Assign permissions to the role
    $role->syncPermissions($permissions); // You can also use givePermissionTo() if preferred

    return response()->json([
        'message' => 'Role created and permissions assigned successfully',
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

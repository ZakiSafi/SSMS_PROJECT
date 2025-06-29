<?php

namespace App\Http\Controllers;

use App\Http\Requests\RollRequest;
use App\Http\Resources\RollResource;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
     private $model = Role::class;
    public function index(Request $request)
    {
        $roles = $this->listRecord($request, $this->model);
        return RollResource::collection($roles);
    }
     
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
}

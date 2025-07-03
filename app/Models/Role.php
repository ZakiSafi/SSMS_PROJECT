<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    // Now you're using Spatie's Role model correctly
    protected $fillable = ["name", "description", "guard_name"];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Role extends Model
{
    Use HasRoles;
    protected $fillable = ["name","description","guard_name"];
}

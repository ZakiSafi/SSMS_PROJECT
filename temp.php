<?php
$adminRole = \Spatie\Permission\Models\Role::findByName("admin");
$permissions = \Spatie\Permission\Models\Permission::all();
$adminRole->syncPermissions($permissions);
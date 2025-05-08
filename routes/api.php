<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\LogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\RollPermissionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StudentStatisticController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\UserController;


Route::apiResource('provinces', ProvinceController::class);
Route::apiResource('universities', UniversityController::class);
Route::apiResource('academicYears', AcademicYearController::class);
Route::apiResource('faculties', FacultyController::class);
Route::apiResource('departments', DepartmentController::class);
Route::apiResource('classRoom', ClassroomController::class);
Route::apiResource('studentStatistics', StudentStatisticController::class);
Route::apiResource('settings', SettingController::class);
Route::apiResource('rollPermissions', RollPermissionController::class);
Route::apiResource('users', UserController::class);
Route::apiResource('logs', LogController::class);

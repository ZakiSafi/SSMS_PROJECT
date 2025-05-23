<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\RollPermissionController;
use App\Http\Controllers\StudentStatisticController;
use App\Http\Controllers\reports\FacultyReportController;
use App\Http\Controllers\reports\ClassRoomReportController;
use App\Http\Controllers\reports\DepartmentReportController;
use App\Http\Controllers\reports\UniversityReportController;
use App\Http\Controllers\reports\UniversityClassReportController;
use App\Http\Controllers\reports\UniversityClassShiftReportController;

Route::apiResource('provinces', ProvinceController::class);
Route::apiResource('universities', UniversityController::class);
Route::apiResource('faculties', FacultyController::class);
Route::apiResource('departments', DepartmentController::class);
Route::apiResource('studentStatistics', StudentStatisticController::class);
Route::apiResource('settings', SettingController::class);
Route::apiResource('rollPermissions', RollPermissionController::class);
Route::apiResource('users', UserController::class);
Route::put('users/update/{user}', [UserController::class, 'update']);
Route::apiResource('logs', LogController::class);

Route::prefix('report')->group(function () {
    Route::get('universitiesClasses', [UniversityClassReportController::class, '__invoke']);
    Route::get('university', [UniversityReportController::class, '__invoke']);
    // Route::get('classRoomReport', [ClassRoomReportController::class, '__invoke']);
    // Route::get('departmentReport', [DepartmentReportController::class, '__invoke']);
    // Route::get('facultyReport', [FacultyReportController::class, '__invoke']);
});

Route::post('login', [AuthController::class, 'login']);

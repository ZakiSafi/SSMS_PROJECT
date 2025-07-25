<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\RollPermissionController;
use App\Http\Controllers\StudentStatisticController;

use App\Http\Controllers\reports\UniversityReportController;
use App\Http\Controllers\reports\StudentsTypeBasedController;
use App\Http\Controllers\reports\DepartmentClassBasedController;
use App\Http\Controllers\reports\StudentTeacherReportController;
use App\Http\Controllers\reports\UniversityClassReportController;
use App\Http\Controllers\reports\FacultyClassBasedReportController;
use App\Http\Controllers\reports\FacultyBaseGraduationReportController;
use App\Http\Controllers\reports\DepartmentBasedGraduationReportController;
use App\Http\Controllers\reports\UniversityBasedGraduationReportController;
use App\Http\Controllers\RoleController;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('provinces', ProvinceController::class);
    Route::apiResource('faculties', FacultyController::class);
    Route::apiResource('departments', DepartmentController::class);
    Route::apiResource('studentStatistics', StudentStatisticController::class);
    Route::apiResource('settings', SettingController::class);
    Route::apiResource('role', RoleController::class);
    Route::apiResource('users', UserController::class);
    Route::put('users/update/{user}', [UserController::class, 'update']);
    Route::get('/me', [UserController::class, 'me']);
    Route::apiResource('logs', LogController::class);
    Route::apiResource('universities', UniversityController::class);
    Route::apiResource('teachers', TeacherController::class);

    

    Route::prefix('report')->group(function () {
    Route::get('universitiesClasses', [UniversityClassReportController::class, '__invoke']);
    Route::get('university', [UniversityReportController::class, '__invoke']);
    Route::get('studentsTypeBased', [StudentsTypeBasedController::class, '__invoke']);
    Route::get('facultyClassBased', [FacultyClassBasedReportController::class, '__invoke']);
    Route::get('departmentClassBase', [DepartmentClassBasedController::class, '__invoke']);
    Route::get('studentTeacherRatio', [StudentTeacherReportController::class, '__invoke']);
    Route::get('facultyBasedGraduation', [FacultyBaseGraduationReportController::class, '__invoke']);
    Route::get('departmentBasedGraduation', [DepartmentBasedGraduationReportController::class, '__invoke']);
    Route::get('universityBaseGraduation', [UniversityBasedGraduationReportController::class, '__invoke']);
});


    // Define the login route (with optional name)

    // Protected Sanctum routes
    Route::post('logout', [AuthController::class, 'logout']);
});


//

Route::post('login', [AuthController::class, 'login'])->name('login');
// reports
// Dashboard routes
    Route::prefix('dashboard')->group(function () {
        Route::get('/summary', [DashboardController::class, 'summary']);
        Route::get('/trends', [DashboardController::class, 'trends']);
        Route::get('/gender-distribution', [DashboardController::class, 'genderDistribution']);
        Route::get('/faculty-breakdown', [DashboardController::class, 'facultyBreakdown']);
        Route::get('/university-comparison', [DashboardController::class, 'universityComparison']);
        Route::get('/recent-activity', [DashboardController::class, 'recentActivity']);
    });

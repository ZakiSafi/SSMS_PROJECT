<?php

use App\Http\Controllers\AcademicYearController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\UniversityController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::apiResource('provinces', ProvinceController::class);
Route::apiResource('universities', UniversityController::class);
Route::apiResource('academic_years', AcademicYearController::class);

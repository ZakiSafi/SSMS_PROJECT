<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/{any}', function () {
    return view('welcome'); // or whatever your SPA Blade file is
})->where('any', '.*');

<?php

use App\Http\Controllers\AcademicController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/academic/courses', [AcademicController::class, 'courses'])
    ->middleware([
        'measure.response.time',
        'trace.request',
        'require.client.key',
    ]);
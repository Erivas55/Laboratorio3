<?php

use App\Http\Controllers\AcademicController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/academic/courses', [AcademicController::class, 'courses'])
    ->middleware([
        'trace.request',
        'require.client.key',
        'measure.response.time',
    ]);
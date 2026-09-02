<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->controller(AuthController::class)->name('auth.')->group(function () {

    Route::post('/login', 'login')->name('login');
    Route::post('/forgot-password', 'forgotPassword')->middleware('throttle:5,1') ->name('forgot-password');
    Route::post('/reset-password', 'resetPassword')->middleware('throttle:5,1')->name('reset-password');
    Route::post('/logout', 'logout')->middleware('auth')->name('logout');
});

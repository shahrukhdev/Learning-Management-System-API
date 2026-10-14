<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Profile\ProfileController;
use App\Http\Controllers\Api\V1\System\PermissionController;
use App\Http\Controllers\Api\V1\System\RoleController;
use App\Http\Controllers\Api\V1\System\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::prefix('auth')->controller(AuthController::class)->name('auth.')->group(function () {

    Route::post('/login', 'login')->name('login');
    Route::post('/forgot-password', 'forgotPassword')->name('forgot-password');
    Route::post('/reset-password', 'resetPassword')->name('reset-password');
    Route::post('/logout', 'logout')->middleware('auth:sanctum')->name('logout');
});

// User Routes
Route::middleware('auth:sanctum')->prefix('users')->controller(UserController::class)->name('users.')->group(function () {

    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::get('/{id}', 'show')->name('show');
    Route::put('/{id}', 'update')->name('update');
    Route::delete('/{id}', 'destroy')->name('destroy');

});

// Role Routes
Route::middleware('auth:sanctum')->prefix('roles')->controller(RoleController::class)->name('roles.')->group(function () {

    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::get('/{id}', 'show')->name('show');
    Route::put('/{id}', 'update')->name('update');
    Route::delete('/{id}', 'destroy')->name('destroy');
});

// Permission Routes
Route::middleware('auth:sanctum')->prefix('permissions')->controller(PermissionController::class)->name('permissions.')->group(function () {
    
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::get('/{id}', 'show')->name('show');
    Route::put('/{id}', 'update')->name('update');
    Route::delete('/{id}', 'destroy')->name('destroy');
});

// Profile Routes
Route::middleware('auth:sanctum')->prefix('profile')->controller(ProfileController::class)->name('profile.')->group(function () {
    Route::get('/', 'show')->name('show');
    Route::patch('/', 'update')->name('update');
});

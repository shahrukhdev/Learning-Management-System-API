<?php

use App\Http\Controllers\Api\V1\Academic\AcademicClassController;
use App\Http\Controllers\Api\V1\Academic\AttendanceController;
use App\Http\Controllers\Api\V1\Academic\CourseController;
use App\Http\Controllers\Api\V1\Academic\StudentController;
use App\Http\Controllers\Api\V1\Academic\StudentParentController;
use App\Http\Controllers\Api\V1\Academic\StudentProgressController;
use App\Http\Controllers\Api\V1\Academic\TeacherController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Dashboard\DashboardController;
use App\Http\Controllers\Api\V1\Hifz\HifzSessionController;
use App\Http\Controllers\Api\V1\Hifz\HifzStatisticsController;
use App\Http\Controllers\Api\V1\Hifz\JuzController;
use App\Http\Controllers\Api\V1\Hifz\QuranMapController;
use App\Http\Controllers\Api\V1\Hifz\SurahController;
use App\Http\Controllers\Api\V1\Hifz\StudentSurahProgressController;
use App\Http\Controllers\Api\V1\Notice\NoticeController;
use App\Http\Controllers\Api\V1\Profile\ProfileController;
use App\Http\Controllers\Api\V1\System\ModuleController;
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

// Module Routes
Route::middleware('auth:sanctum')->prefix('modules')->controller(ModuleController::class)->name('module.')->group(function () {

    Route::get('/', 'index')->name('index');
});

// Profile Routes
Route::middleware('auth:sanctum')->prefix('profile')->controller(ProfileController::class)->name('profile.')->group(function () {
    Route::get('/', 'show')->name('show');
    Route::patch('/', 'update')->name('update');
});

// Course Routes
Route::middleware('auth:sanctum')->prefix('courses')->controller(CourseController::class)->name('courses.')->group(function () {
    Route::apiResource('/', CourseController::class)
        ->parameters(['' => 'id']);
});

// Academic Class Routes
Route::middleware('auth:sanctum')->prefix('academic-classes')->controller(AcademicClassController::class)->name('academic-classes.')->group(function () {
    Route::apiResource('/', AcademicClassController::class)
        ->parameters(['' => 'id']);
});

// Student Routes
Route::middleware('auth:sanctum')->prefix('students')->controller(StudentController::class)->name('students.')->group(function () {
    Route::apiResource('/', StudentController::class)
        ->parameters(['' => 'id']);
});

// Student Progress Routes
Route::middleware('auth:sanctum')->prefix('student-progress')->controller(StudentProgressController::class)->name('student-progress.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{studentId}', 'show')->whereNumber('studentId')->name('show');
    Route::get('/{studentId}/records', 'records')->whereNumber('studentId')->name('records');
    Route::post('/{studentId}/records', 'store')->whereNumber('studentId')->name('store');
    Route::get('/{studentId}/records/{id}', 'showRecord')->whereNumber(['studentId', 'id'])->name('records.show');
    Route::put('/{studentId}/records/{id}', 'update')->whereNumber(['studentId', 'id'])->name('update');
    Route::delete('/{studentId}/records/{id}', 'destroy')->whereNumber(['studentId', 'id'])->name('destroy');
});

// Attendance Routes
Route::middleware('auth:sanctum')->prefix('attendances')->controller(AttendanceController::class)->name('attendances.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::get('/overview', 'overview')->name('overview');
    Route::get('/my-attendance', 'myAttendance')->name('my-attendance');
});

// Notice Routes
Route::middleware('auth:sanctum')->prefix('notices')->controller(NoticeController::class)->name('notices.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::get('/{id}', 'show')->name('show');
    Route::put('/{id}', 'update')->name('update');
    Route::delete('/{id}', 'destroy')->name('destroy');
});

// Teacher Routes
Route::middleware('auth:sanctum')->prefix('teachers')->controller(TeacherController::class)->name('teachers.')->group(function () {
    Route::apiResource('/', TeacherController::class)
        ->parameters(['' => 'id']);
});

// Parent Routes
Route::middleware('auth:sanctum')->prefix('parents')->controller(StudentParentController::class)->name('parents.')->group(function () {
    Route::apiResource('/', StudentParentController::class)
        ->parameters(['' => 'id']);
});

// Surah Routes
Route::middleware('auth:sanctum')->prefix('surahs')->controller(SurahController::class)->name('surahs.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{id}', 'show')->name('show');
});

// Juz Routes
Route::middleware('auth:sanctum')->prefix('juz')->controller(JuzController::class)->name('juz.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{id}', 'show')->name('show');
});

// Student Surah Progress Routes
Route::middleware('auth:sanctum')->prefix('students/{studentId}/surah-progress')->controller(StudentSurahProgressController::class)->name('students.surah-progress.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
});

Route::middleware('auth:sanctum')->prefix('surah-progress')->controller(StudentSurahProgressController::class)->name('surah-progress.')->group(function () {
    Route::put('/{id}', 'update')->name('update');
    Route::delete('/{id}', 'destroy')->name('destroy');
});

// Hifz Session Routes
Route::middleware('auth:sanctum')->prefix('students/{studentId}/hifz-sessions')->controller(HifzSessionController::class)->name('students.hifz-sessions.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::get('/{id}', 'show')->name('show');
    Route::put('/{id}', 'update')->name('update');
    Route::delete('/{id}', 'destroy')->name('destroy');
});

// Quran Map Routes
Route::middleware('auth:sanctum')->prefix('students/{studentId}/quran-map')->controller(QuranMapController::class)->name('students.quran-map.')->group(function () {
    Route::get('/', 'index')->name('index');
});

// Dashboard Routes
Route::middleware('auth:sanctum')->prefix('dashboard')->controller(DashboardController::class)->name('dashboard.')->group(function () {
    Route::get('/summary', 'summary')->name('summary');
    Route::get('/student-summary', 'studentSummary')->name('student-summary');
});

// Hifz Statistics Routes
Route::middleware('auth:sanctum')->prefix('students/{studentId}/hifz-statistics')->controller(HifzStatisticsController::class)->name('students.hifz-statistics.')->group(function () {
    Route::get('/', 'show')->name('show');
});


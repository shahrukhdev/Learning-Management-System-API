<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;


Route::get('/', function () {
    // return view('welcome');
    abort(404);
});


Route::get('/test-storage/{filename}', function ($filename) {
    $path = 'users/' . $filename;

    abort_unless(Storage::disk('public')->exists($path), 404);

    return response()->file(
        Storage::disk('public')->path($path)
    );
});
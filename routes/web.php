<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/storage/{path}', function ($path) {
    $path = storage_path('app/public/' . $path);
    if (!file_exists($path)) {
        abort(404);
    }
    return response()->file($path);
})->where('path', '.*');

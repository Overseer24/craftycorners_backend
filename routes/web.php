<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\api\AuthController;

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

//landing page
Route::inertia('/', 'Landing')->name('landing');

Route::inertia('/about', 'About')->name('about');


Route::get('/email', function () {
    return new \App\Mail\ReportResolved(App\Models\Post::first());
})->name('email');


Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

Route::middleware('guest')->group(function () {

    Route::inertia('/login', 'Auth/Login')->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::inertia('/register', 'Auth/Register')->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});


Route::inertia('/register', 'Auth/Register')->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::inertia('/login', 'Auth/Login')->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Route::inertia('dashboard', 'Dashboard')->name('dashboard');


Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::inertia('/about', 'About')->name('about');
//Route::get('/sanctum/csrf-cookie', function () {
//    return response()->json(['message' => 'CSRF cookie set']);
//});

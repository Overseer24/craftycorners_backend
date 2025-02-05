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
Route::inertia('/', 'Home')->name('home');

Route::inertia('/about', 'About')->name('about');


Route::get('/email', function () {
    return new \App\Mail\ReportResolved(App\Models\Post::first());
})->name('email');


Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia\Inertia::render('Dashboard');
})->name('dashboard');

Route::inertia('/register', 'Auth/Register')->name('register');

Route::post('/register', [AuthController::class, 'register']);



//Route::get('/sanctum/csrf-cookie', function () {
//    return response()->json(['message' => 'CSRF cookie set']);
//});

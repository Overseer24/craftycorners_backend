<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::get('/', function () {
    return Inertia::render('Home');
});

Route::get('/email',function (){
    return new \App\Mail\ReportResolved(App\Models\Post::first());
})->name('email');

//Route::get('/sanctum/csrf-cookie', function () {
//    return response()->json(['message' => 'CSRF cookie set']);
//});

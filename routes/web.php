<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


// Dashboard routes.
Route::middleware(['auth:web','verified'])->group(function () {

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/clients', function () {
        return Inertia::render('Clients/Index');
    })->name('clients.index');

    Route::resource('contacts', \App\Http\Controllers\Dashboard\ContactController::class);
});

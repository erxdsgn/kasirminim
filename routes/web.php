<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Halaman depan (public)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('index');
})->name('home');

Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

Route::view('/portfolio-details', 'portfolio-details')->name('portfolio-details');
Route::view('/service-details', 'service-details')->name('service-details');

/*
|--------------------------------------------------------------------------
| Login / Logout
|--------------------------------------------------------------------------
*/

// Hapus baris showLoginForm ini kalau tombol login kamu adalah modal di halaman depan,
// bukan halaman /login terpisah.
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Area Admin / Dashboard (perlu login)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

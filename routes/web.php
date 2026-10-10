<?php

use App\Http\Controllers\KasirController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Halaman Depan (Welcome Page)
Route::get('/', function () {
    return view('welcome');
});

// Group route yang wajib Login & Terverifikasi
Route::middleware(['auth', 'verified'])->group(function () {

    // Route Dashboard & Kasir
    Route::get('/dashboard', [KasirController::class, 'index'])->name('dashboard');

    Route::prefix('kasir')->name('kasir.')->group(function () {
        Route::get('/', [KasirController::class, 'index'])->name('index');

        // Transaksi
        Route::post('transaksi', [KasirController::class, 'store'])->name('store');
        Route::post('tahan', [KasirController::class, 'hold'])->name('hold');

        // Transaksi ditahan
        Route::get('ditahan', [KasirController::class, 'held'])->name('held');
        Route::delete('ditahan/{transaction}', [KasirController::class, 'destroyHeld'])->name('held.destroy');

        // Riwayat & pembatalan
        Route::get('riwayat', [KasirController::class, 'history'])->name('history');
        Route::post('riwayat/{transaction}/batal', [KasirController::class, 'cancel'])->name('cancel');

        // Struk & tutup kasir
        Route::get('struk/{transaction}', [KasirController::class, 'receipt'])->name('receipt');
        Route::get('tutup', [KasirController::class, 'closing'])->name('closing');
    });

    // Profile Routes bawaan Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Memuat rute autentikasi Breeze (Login, Register, Logout, Reset Password, dll.)
require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\KasirController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\MidtransController;
use Illuminate\Support\Facades\Route;

// Halaman Depan (Welcome Page)
Route::get('/', function () {
    return view('welcome');
});

Route::post('/midtrans/notification', [MidtransController::class, 'notification']);

// Group route yang wajib Login & Terverifikasi
Route::middleware(['auth', 'verified'])->group(function () {

    Route::post('/checkout', [MidtransController::class, 'checkout'])->name('checkout');

    // Route Dashboard & Kasir
    Route::get('/dashboard', [KasirController::class, 'index'])->name('dashboard');

    Route::prefix('kasir')->name('kasir.')->group(function () {
        Route::get('/', [KasirController::class, 'index'])->name('index');

        // Transaksi Kasir
        Route::post('transaksi', [KasirController::class, 'store'])->name('store');
        Route::post('tahan', [KasirController::class, 'hold'])->name('hold');

        // Transaksi Ditahan
        Route::get('ditahan', [KasirController::class, 'held'])->name('held');
        Route::delete('ditahan/{transaction}', [KasirController::class, 'destroyHeld'])->name('held.destroy');

        // Riwayat & Pembatalan
        Route::get('riwayat', [KasirController::class, 'history'])->name('history');
        Route::post('riwayat/{transaction}/batal', [KasirController::class, 'cancel'])->name('cancel');

        // Struk Web & Tutup Kasir
        Route::get('struk/{transaction}', [KasirController::class, 'receipt'])->name('receipt');
        Route::get('tutup', [KasirController::class, 'closing'])->name('closing');

        // ROUTE ACARA 35 & 36: CETAK STRUK THERMAL (ESC/POS)
        Route::get('print-test', [ReceiptController::class, 'printTest'])->name('print.test');
        Route::post('print-thermal', [ReceiptController::class, 'printTransaction'])->name('print.thermal');
    });

    // Resource Products & PDF Laporan
    Route::resource('products', ProductController::class);
    Route::get('/laporan-pdf', [LaporanController::class, 'generatePDF'])->name('laporan.pdf');

    // Profile Routes bawaan Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

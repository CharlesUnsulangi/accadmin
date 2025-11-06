<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// COA Pure JavaScript (No Authentication Required)
Route::prefix('coa-js')->name('coa.js.')->group(function () {
    Route::get('/', [\App\Http\Controllers\CoaController::class, 'index'])->name('index');
    Route::get('/data', [\App\Http\Controllers\CoaController::class, 'getData'])->name('data');
    Route::get('/filters', [\App\Http\Controllers\CoaController::class, 'getFilters'])->name('filters');
    Route::post('/store', [\App\Http\Controllers\CoaController::class, 'store'])->name('store');
    Route::put('/{code}', [\App\Http\Controllers\CoaController::class, 'update'])->name('update');
    Route::delete('/{code}', [\App\Http\Controllers\CoaController::class, 'destroy'])->name('destroy');
    Route::get('/hierarchy', [\App\Http\Controllers\CoaController::class, 'hierarchy'])->name('hierarchy');
});

// COA Alpine.js (No Authentication Required)
Route::get('/coa-alpine', [\App\Http\Controllers\CoaController::class, 'alpine'])->name('coa.alpine');

// COA Bootstrap (No Authentication Required)
Route::get('/coa-bootstrap', [\App\Http\Controllers\CoaController::class, 'bootstrap'])->name('coa.bootstrap');

// COA jQuery AJAX (No Authentication Required)
Route::get('/coa-jquery', [\App\Http\Controllers\CoaController::class, 'jquery'])->name('coa.jquery');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // COA Management Routes - Modern (H1-H6 Flexible Hierarchy) - BOOTSTRAP VERSION
    Route::get('/coa-modern', \App\Livewire\CoaModernManagement::class)->name('coa.modern');
    
    // COA Management Routes - Legacy (4 Level: Main > Sub1 > Sub2 > COA) - BOOTSTRAP VERSION
    Route::get('/coa-legacy', \App\Livewire\CoaLegacy::class)->name('coa.legacy');
    
    // COA Legacy Management - Individual Tables - BOOTSTRAP VERSION
    Route::get('/coa-main', \App\Livewire\CoaMainManagement::class)->name('coa.main');
    
    // COA Full Hierarchy Report (4 Level JOIN) - BOOTSTRAP VERSION
    Route::get('/coa-full-hierarchy', \App\Livewire\CoaFullHierarchy::class)->name('coa.hierarchy');
    
    // Jurnal Transaksi - BOOTSTRAP VERSION
    Route::get('/jurnal-transaksi', \App\Livewire\JurnalTransaksi::class)->name('jurnal.transaksi');
    
    // Cheque Management - BOOTSTRAP VERSION
    Route::get('/cheque-management', \App\Livewire\ChequeManagement::class)->name('cheque.management');
    
    // COA Management Routes - Old (redirect to modern)
    Route::get('/coa', function () {
        return redirect()->route('coa.modern');
    })->name('coa.index');
});

require __DIR__.'/auth.php';

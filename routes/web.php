<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // COA Management Routes - Modern (H1-H6 Flexible Hierarchy)
    Route::get('/coa-modern', \App\Livewire\CoaModernManagement::class)->name('coa.modern');
    
    // COA Management Routes - Legacy (4 Level: Main > Sub1 > Sub2 > COA)
    Route::get('/coa-legacy', \App\Livewire\CoaLegacy::class)->name('coa.legacy');
    
    // COA Legacy Management - Individual Tables
    Route::get('/coa-main', \App\Livewire\CoaMainManagement::class)->name('coa.main');
    
    // COA Full Hierarchy Report (4 Level JOIN)
    Route::get('/coa-full-hierarchy', \App\Livewire\CoaFullHierarchy::class)->name('coa.hierarchy');
    
    // COA Management Routes - Old (redirect to modern)
    Route::get('/coa', function () {
        return redirect()->route('coa.modern');
    })->name('coa.index');
});

require __DIR__.'/auth.php';

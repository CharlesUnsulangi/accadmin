<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Dashboard - Accounting
Route::get('/dashboard/accounting', [\App\Http\Controllers\DashboardController::class, 'accounting'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.accounting');

// Dashboard - Admin IT
Route::get('/dashboard/admin-it', [\App\Http\Controllers\DashboardController::class, 'adminIt'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.admin.it');

// System Overview (Alpine.js)
Route::get('/overview', [\App\Http\Controllers\DashboardController::class, 'overview'])
    ->middleware(['auth', 'verified'])
    ->name('overview');

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
    
    // Cheque Create - Alpine.js Version (New Page)
    Route::get('/cheque/create', [\App\Http\Controllers\ChequeController::class, 'create'])->name('cheque.create');
    
    // Transaksi Cheque - BOOTSTRAP VERSION
    Route::get('/transaksi-cheque', \App\Livewire\TransaksiChequeManagement::class)->name('transaksi.cheque');
    
    // ========================================
    // MASTER DATA ROUTES (Alpine.js + API)
    // ========================================
    
    // Master Data Dashboard
    Route::get('/master', [\App\Http\Controllers\MasterDataController::class, 'dashboard'])->name('master.dashboard');
    
    // Master Data - Bank (Alpine.js)
    Route::get('/master/bank', function() {
        return view('master.bank');
    })->name('master.bank');
    
    // Master Data - Area (Alpine.js) 
    Route::get('/master/area', function() {
        return view('master.area');
    })->name('master.area');
    
    // Master Data - Vendor (Alpine.js)
    Route::get('/master/vendor', function() {
        return view('master.vendor');
    })->name('master.vendor');
    
    // Old Livewire routes (keep for backward compatibility)
    Route::get('/master-transaksi', \App\Livewire\TransaksiManagement::class)->name('master.transaksi');
    Route::get('/master-status-cheque', \App\Livewire\StatusChequeManagement::class)->name('master.statuscheque');
    
    // IT Documentation - BOOTSTRAP VERSION
    Route::get('/it-documentation', \App\Livewire\ItDocumentation::class)->name('it.documentation');

    // Documentation - Tables (Database Table Browser)
    Route::get('/docs-tables', \App\Livewire\DocsTables::class)->name('docs.tables');
    
    // Admin SP Management - BOOTSTRAP VERSION
    Route::get('/admin-sp', \App\Livewire\AdminSpManagement::class)->name('admin.sp');
    
    // Database Tables - Alpine.js Version (Non-Livewire)
    Route::get('/database-tables', [\App\Http\Controllers\DatabaseTablesController::class, 'index'])->name('database.tables');
    Route::get('/table-detail/{tableName}', [\App\Http\Controllers\DatabaseTablesController::class, 'detail'])->name('table.detail');
    
    // Application Management (Diagnostic Tool)
    Route::get('/applications', [\App\Http\Controllers\ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{id}', [\App\Http\Controllers\ApplicationController::class, 'show'])->name('applications.show');
    
    // Database Tables API endpoints
    Route::prefix('api/database-tables')->group(function () {
        Route::get('/data', [\App\Http\Controllers\DatabaseTablesController::class, 'getData']);
        Route::post('/update-metadata', [\App\Http\Controllers\DatabaseTablesController::class, 'updateMetadata']);
        Route::post('/update-all-metadata', [\App\Http\Controllers\DatabaseTablesController::class, 'updateAllMetadata']);
        Route::post('/toggle-priority/{tableId}', [\App\Http\Controllers\DatabaseTablesController::class, 'togglePriority']);
        
        // Sync & Scan endpoints (SAFE - INSERT NEW ONLY, NO DELETE)
        Route::get('/scan-new-tables', [\App\Http\Controllers\DatabaseTablesController::class, 'scanNewTables']);
        Route::post('/add-new-table', [\App\Http\Controllers\DatabaseTablesController::class, 'addNewTable']);
        
        // Message endpoints
        Route::get('/messages/{tableId}', [\App\Http\Controllers\DatabaseTablesController::class, 'getMessages']);
        Route::post('/add-message', [\App\Http\Controllers\DatabaseTablesController::class, 'addMessage']);
        Route::delete('/delete-message/{messageId}', [\App\Http\Controllers\DatabaseTablesController::class, 'deleteMessage']);
    });
    
    // Master Data API endpoints
    Route::prefix('api/master')->group(function () {
        Route::get('/stats', [\App\Http\Controllers\MasterDataController::class, 'getStats']);
        
        // Bank API
        Route::get('/bank/data', [\App\Http\Controllers\MasterDataController::class, 'getBankData']);
        Route::post('/bank/store', [\App\Http\Controllers\MasterDataController::class, 'storeBank']);
        Route::put('/bank/{code}', [\App\Http\Controllers\MasterDataController::class, 'updateBank']);
        Route::delete('/bank/{code}', [\App\Http\Controllers\MasterDataController::class, 'deleteBank']);
        Route::get('/bank/export', [\App\Http\Controllers\MasterDataController::class, 'exportBank']);
    });

    // Application Management API endpoints
    Route::prefix('api/applications')->group(function () {
        Route::get('/data', [\App\Http\Controllers\ApplicationController::class, 'getData']);
        Route::get('/stats', [\App\Http\Controllers\ApplicationController::class, 'getStats']);
        Route::post('/store', [\App\Http\Controllers\ApplicationController::class, 'store']);
        Route::put('/update/{id}', [\App\Http\Controllers\ApplicationController::class, 'update']);
        Route::post('/toggle-status/{id}', [\App\Http\Controllers\ApplicationController::class, 'toggleStatus']);
        // NO DELETE - hanya toggle status
        
        // Application Topics (one-to-many relationship)
        Route::get('/{id}/topics', [\App\Http\Controllers\ApplicationController::class, 'getTopics']);
        Route::post('/{id}/topics', [\App\Http\Controllers\ApplicationController::class, 'storeTopic']);
        Route::put('/{id}/topics/{topicId}', [\App\Http\Controllers\ApplicationController::class, 'updateTopic']);
        Route::delete('/{id}/topics/{topicId}', [\App\Http\Controllers\ApplicationController::class, 'deleteTopic']);
    });

    // Dashboard API endpoints
    Route::prefix('api/dashboard')->group(function () {
        Route::get('/accounting-stats', [\App\Http\Controllers\DashboardController::class, 'getAccountingStats']);
        Route::get('/admin-it-stats', [\App\Http\Controllers\DashboardController::class, 'getAdminItStats']);
        Route::get('/overview-stats', [\App\Http\Controllers\DashboardController::class, 'getOverviewStats']);
    });

    // Cheque API endpoints
    Route::prefix('api/cheque')->group(function () {
        Route::post('/store', [\App\Http\Controllers\ChequeController::class, 'store']);
    });
    
    // Table Column API endpoints
    Route::prefix('api/table-columns')->group(function () {
        Route::post('/update-comment', [\App\Http\Controllers\DatabaseTablesController::class, 'updateColumnComment']);
    });
    
    // Table Access Statistics - Monitor table usage
    Route::get('/table-access-stats', \App\Livewire\TableAccessStats::class)->name('table.access.stats');
    
    // COA Management Routes - Old (redirect to modern)
    Route::get('/coa', function () {
        return redirect()->route('coa.modern');
    })->name('coa.index');
    
    // Closing & Audit Routes
    Route::get('/closing/balance-sheet', \App\Livewire\BalanceSheetReport::class)->name('closing.balance-sheet');
    
    Route::get('/closing/version-history', \App\Livewire\VersionHistoryViewer::class)->name('closing.version-history');
    Route::get('/closing/comparison', \App\Livewire\VersionComparison::class)->name('closing.comparison');
    Route::get('/closing/rollback', \App\Livewire\RollbackInterface::class)->name('closing.rollback');
});

// Closing Process (Alpine.js - No Livewire) - NO AUTH untuk testing
Route::get('/closing/process', [\App\Http\Controllers\ClosingProcessController::class, 'index'])->name('closing.process');
Route::post('/closing/preview', [\App\Http\Controllers\ClosingProcessController::class, 'preview'])->name('closing.preview');
Route::post('/closing/generate', [\App\Http\Controllers\ClosingProcessController::class, 'generate'])->name('closing.generate');
Route::post('/closing/lock', [\App\Http\Controllers\ClosingProcessController::class, 'lock'])->name('closing.lock');
Route::get('/closing/existing', [\App\Http\Controllers\ClosingProcessController::class, 'getExisting'])->name('closing.existing');
Route::post('/closing/compare-audit', [\App\Http\Controllers\ClosingProcessController::class, 'compareAudit'])->name('closing.compare-audit');

// Closing History
Route::get('/closing/history', [\App\Http\Controllers\ClosingProcessController::class, 'history'])->name('closing.history');
Route::get('/closing/history/data', [\App\Http\Controllers\ClosingProcessController::class, 'historyData'])->name('closing.history.data');
Route::get('/closing/history/export', [\App\Http\Controllers\ClosingProcessController::class, 'historyExport'])->name('closing.history.export');

require __DIR__.'/auth.php';

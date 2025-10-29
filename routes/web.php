<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\BusinessManagementController;
use App\Http\Controllers\Admin\MatrimonyManagementController;
use App\Http\Controllers\Admin\PaymentManagementController;
use App\Http\Controllers\Admin\CategoryManagementController;
use App\Http\Controllers\Admin\HomepageHeroController;


// Public route
Route::get('/', function () {
    return view('welcome');
});

Route::resource('heroes', HomepageHeroController::class);

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest admin routes
    Route::middleware('guest')->group(function () {
        Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AdminAuthController::class, 'login'])->name('login.post');
    });
    
    // Authenticated admin routes
    Route::middleware('auth')->group(function () {
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
        
        // Dashboard
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('analytics', [AdminDashboardController::class, 'analytics'])->name('analytics');
        
        // User Management Routes
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserManagementController::class, 'index'])->name('index');
            Route::get('/pending', [UserManagementController::class, 'pending'])->name('pending');
            Route::post('/{id}/approve-certificate', [UserManagementController::class, 'approveCertificate'])->name('approve-certificate');
            Route::post('/{id}/reject-certificate', [UserManagementController::class, 'rejectCertificate'])->name('reject-certificate');
            Route::get('/{id}', [UserManagementController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [UserManagementController::class, 'edit'])->name('edit');
            Route::put('/{id}', [UserManagementController::class, 'update'])->name('update');
            Route::delete('/{id}', [UserManagementController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/suspend', [UserManagementController::class, 'suspend'])->name('suspend');
            Route::post('/{id}/activate', [UserManagementController::class, 'activate'])->name('activate');
            Route::post('/{id}/verify', [UserManagementController::class, 'verify'])->name('verify');
            Route::get('/verification/pending', [UserManagementController::class, 'pendingVerifications'])->name('verification.pending');
        });
        
        // Category Management Routes
        Route::prefix('category')->name('category.')->group(function () {
            Route::get('/', [CategoryManagementController::class, 'index'])->name('index');
            Route::get('/addcategory', [CategoryManagementController::class, 'addcategory'])->name('addcategory');
            Route::post('/storecategory', [CategoryManagementController::class, 'storeCategory'])->name('storecategory');
            Route::post('/{id}/active', [CategoryManagementController::class, 'active'])->name('active');
            Route::post('/{id}/inactive', [CategoryManagementController::class, 'inactive'])->name('inactive');
            Route::post('/{id}/destroy', [CategoryManagementController::class, 'destroy'])->name('destroy');
        });

        // Business Management Routes
        Route::prefix('businesses')->name('businesses.')->group(function () {
            Route::get('/', [BusinessManagementController::class, 'index'])->name('index');
            Route::get('/pending', [BusinessManagementController::class, 'pending'])->name('pending');
            Route::get('/verification', [BusinessManagementController::class, 'verification'])->name('verification');
            Route::post('/{id}/approve', [BusinessManagementController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [BusinessManagementController::class, 'reject'])->name('reject');
            Route::get('/{id}', [BusinessManagementController::class, 'show'])->name('show');
            Route::post('/{id}/suspend', [BusinessManagementController::class, 'suspend'])->name('suspend');
            Route::post('/{id}/activate', [BusinessManagementController::class, 'activate'])->name('activate');
            Route::delete('/{id}', [BusinessManagementController::class, 'destroy'])->name('destroy');
        });
        
        // Matrimony Management Routes
        Route::prefix('matrimony')->name('matrimony.')->group(function () {
            Route::get('/', [MatrimonyManagementController::class, 'index'])->name('index');
            Route::get('/moderation', [MatrimonyManagementController::class, 'moderation'])->name('moderation');
            Route::post('/{id}/approve', [MatrimonyManagementController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [MatrimonyManagementController::class, 'reject'])->name('reject');
            Route::get('/{id}', [MatrimonyManagementController::class, 'show'])->name('show');
            Route::post('/{id}/suspend', [MatrimonyManagementController::class, 'suspend'])->name('suspend');
            Route::post('/{id}/activate', [MatrimonyManagementController::class, 'activate'])->name('activate');
            Route::get('/connections', [MatrimonyManagementController::class, 'connections'])->name('connections');
            Route::get('/chats', [MatrimonyManagementController::class, 'chats'])->name('chats');
            Route::delete('/{id}', [MatrimonyManagementController::class, 'destroy'])->name('destroy');
        });
        
        // Payment Management Routes
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/', [PaymentManagementController::class, 'index'])->name('index');
            Route::get('/analytics', [PaymentManagementController::class, 'analytics'])->name('analytics');
            Route::get('/{id}', [PaymentManagementController::class, 'show'])->name('show');
            Route::post('/{id}/refund', [PaymentManagementController::class, 'refund'])->name('refund');
            Route::post('/{id}/update-status', [PaymentManagementController::class, 'updateStatus'])->name('update-status');
            Route::get('/export', [PaymentManagementController::class, 'export'])->name('export');
        });
        
        // Settings and Reports
         Route::get('settings', [AdminDashboardController::class, 'settings'])->name('settings');
         Route::post('settings', [AdminDashboardController::class, 'updateSettings'])->name('settings.update');
         Route::get('reports', [AdminDashboardController::class, 'reports'])->name('reports');
         
         // System Actions
         Route::post('system/clear-cache', [AdminDashboardController::class, 'clearCache'])->name('system.clear-cache');
         Route::post('system/run-migrations', [AdminDashboardController::class, 'runMigrations'])->name('system.run-migrations');
         Route::post('system/optimize', [AdminDashboardController::class, 'optimizeApp'])->name('system.optimize');
         Route::post('system/clear-logs', [AdminDashboardController::class, 'clearLogs'])->name('system.clear-logs');
         Route::post('system/generate-key', [AdminDashboardController::class, 'generateKey'])->name('system.generate-key');
         Route::post('system/storage-link', [AdminDashboardController::class, 'createStorageLink'])->name('system.storage-link');
         Route::post('system/backup-database', [AdminDashboardController::class, 'backupDatabase'])->name('system.backup-database');
         Route::get('system/info', [AdminDashboardController::class, 'getSystemInfo'])->name('system.info');
    });
});

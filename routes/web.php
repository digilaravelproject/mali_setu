<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\BusinessManagementController;
use App\Http\Controllers\Admin\MatrimonyManagementController;
use App\Http\Controllers\Admin\PaymentManagementController;
use App\Http\Controllers\Admin\DonationManagementController;
use App\Http\Controllers\Admin\CategoryManagementController;
use App\Http\Controllers\Admin\HomepageHeroController;
use App\Http\Controllers\Admin\VolunteerManagementController;
use App\Http\Controllers\Admin\CastManagementController;
use App\Http\Controllers\Admin\PlanManagementController;


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

        // Donation Management Routes
        Route::prefix('donations')->name('donations.')->group(function () {
            // Donation Causes management (must come before parameterized routes)
            Route::get('/causes', [DonationManagementController::class, 'causes'])->name('causes');
            Route::get('/causes/create', [DonationManagementController::class, 'createCause'])->name('causes.create');
            Route::post('/causes', [DonationManagementController::class, 'storeCause'])->name('causes.store');
            Route::get('/causes/{id}/edit', [DonationManagementController::class, 'editCause'])->name('causes.edit');
            Route::put('/causes/{id}', [DonationManagementController::class, 'updateCause'])->name('causes.update');
            Route::delete('/causes/{id}', [DonationManagementController::class, 'destroyCause'])->name('causes.destroy');
            Route::patch('/causes/{id}/toggle-status', [DonationManagementController::class, 'toggleCauseStatus'])->name('causes.toggle-status');
            
            // Donations list and detail
            Route::get('/', [DonationManagementController::class, 'index'])->name('index');
            Route::get('/{id}', [DonationManagementController::class, 'show'])->name('show');
            Route::delete('/{id}', [DonationManagementController::class, 'destroy'])->name('destroy');
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

        // Volunteer Management Routes
        Route::prefix('volunteers')->name('volunteers.')->group(function () {
            Route::get('/', [VolunteerManagementController::class, 'index'])->name('index');
            Route::get('/pending', [VolunteerManagementController::class, 'pending'])->name('pending');
            Route::get('/verification', [VolunteerManagementController::class, 'verification'])->name('verification');
            Route::post('/{id}/approve', [VolunteerManagementController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [VolunteerManagementController::class, 'reject'])->name('reject');
            Route::get('/{id}', [VolunteerManagementController::class, 'show'])->name('show');
            Route::delete('/{id}', [VolunteerManagementController::class, 'destroy'])->name('destroy');
        });

        // Plans Management Routes
        Route::prefix('plans')->name('plans.')->group(function () {
            Route::prefix('business')->name('business.')->group(function () {
                Route::get('/', [PlanManagementController::class, 'businessIndex'])->name('index');
                Route::get('/create', [PlanManagementController::class, 'createBusiness'])->name('create');
                Route::post('/', [PlanManagementController::class, 'storeBusiness'])->name('store');
                Route::get('/{id}/edit', [PlanManagementController::class, 'editBusiness'])->name('edit');
                Route::put('/{id}', [PlanManagementController::class, 'updateBusiness'])->name('update');
                Route::delete('/{id}', [PlanManagementController::class, 'destroyBusiness'])->name('destroy');
            });
            
            // Matrimony plans
            Route::prefix('matrimony')->name('matrimony.')->group(function () {
                Route::get('/', [PlanManagementController::class, 'matrimonyIndex'])->name('index');
                Route::get('/create', [PlanManagementController::class, 'createMatrimony'])->name('create');
                Route::post('/', [PlanManagementController::class, 'storeMatrimony'])->name('store');
                Route::get('/{id}/edit', [PlanManagementController::class, 'editMatrimony'])->name('edit');
                Route::put('/{id}', [PlanManagementController::class, 'updateMatrimony'])->name('update');
                Route::delete('/{id}', [PlanManagementController::class, 'destroyMatrimony'])->name('destroy');
            });
        });

        // Cast Management Routes
        Route::prefix('casts')->name('casts.')->group(function () {
            // Cast routes
            Route::get('/', [CastManagementController::class, 'index'])->name('index');
            Route::get('/create', [CastManagementController::class, 'create'])->name('create');
            Route::post('/', [CastManagementController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [CastManagementController::class, 'edit'])->name('edit');
            Route::put('/{id}', [CastManagementController::class, 'update'])->name('update');
            Route::delete('/{id}', [CastManagementController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/toggle-status', [CastManagementController::class, 'toggleStatus'])->name('toggle-status');

            // SubCast routes
            Route::get('/{castId}/subcasts', [CastManagementController::class, 'subcastIndex'])->name('subcasts.index');
            Route::get('/{castId}/subcasts/create', [CastManagementController::class, 'subcastCreate'])->name('subcasts.create');
            Route::post('/{castId}/subcasts', [CastManagementController::class, 'subcastStore'])->name('subcasts.store');
            Route::get('/{castId}/subcasts/{subCastId}/edit', [CastManagementController::class, 'subcastEdit'])->name('subcasts.edit');
            Route::put('/{castId}/subcasts/{subCastId}', [CastManagementController::class, 'subcastUpdate'])->name('subcasts.update');
            Route::delete('/{castId}/subcasts/{subCastId}', [CastManagementController::class, 'subcastDestroy'])->name('subcasts.destroy');
            Route::patch('/{castId}/subcasts/{subCastId}/toggle-status', [CastManagementController::class, 'subcastToggleStatus'])->name('subcasts.toggle-status');
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

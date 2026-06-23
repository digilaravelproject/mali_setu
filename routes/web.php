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
use App\Http\Controllers\Admin\BlogManagementController;
use App\Http\Controllers\Admin\BlogCategoryManagementController;
use App\Http\Controllers\Admin\BlogCommentManagementController;
use App\Http\Controllers\Admin\EducationManagementController;
use App\Http\Controllers\Admin\PlanManagementController;
use Illuminate\Support\Facades\Mail;

Route::get('/test-mail', function () {

    Mail::raw('This is a test email from Laravel.', function ($message) {
        $message->to('darshankondekar01@gmail.com')
                ->subject('Laravel Test Email');
    });

    return "Test email sent successfully!";
});


use App\Http\Controllers\PageController;
use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\VolunteerController;

// Public route
Route::get('/', function () {
    return view('landing_page');
});

Route::get('/privacy-policy', [PageController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/terms-condition', [PageController::class, 'termsCondition'])->name('terms-condition');
Route::get('/contact-us', [PageController::class, 'contactUs'])->name('contact-us');

// Public Blog Routes
Route::get('/blogs/{id}', [\App\Http\Controllers\BlogController::class, 'showPublic'])->name('blogs.public.show');
Route::post('/blogs/{id}/like', [\App\Http\Controllers\BlogController::class, 'likePublic'])->name('blogs.public.like');

// Guest (Before-Login) Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [WebAuthController::class, 'login']);

    Route::get('/register', [WebAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [WebAuthController::class, 'register']);

    Route::get('/forgot-password', [WebAuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password/otp', [WebAuthController::class, 'sendOtp'])->name('password.otp.send');
    Route::post('/verify-otp', [WebAuthController::class, 'verifyOtp'])->name('password.otp.verify');
    Route::post('/reset-password', [WebAuthController::class, 'resetPassword'])->name('password.update');

    Route::get('/auth/google', [WebAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [WebAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

// Protected (After-Login) Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/profile', [DashboardController::class, 'updateProfile'])->name('dashboard.profile.update');
    Route::post('/dashboard/change-password', [DashboardController::class, 'changePassword'])->name('dashboard.password.change');
    Route::delete('/dashboard/delete-account', [DashboardController::class, 'deleteAccount'])->name('dashboard.account.delete');
    Route::post('/dashboard/update-location', [DashboardController::class, 'updateLocation'])->name('dashboard.update-location');

    // Dedicated Business Management & Directory Routes (Decoupled & Standard MVC)
    Route::get('/dashboard/business/browse', [BusinessController::class, 'browse'])->name('dashboard.business.browse');
    Route::get('/dashboard/business/profile/{id}', [BusinessController::class, 'show'])->name('dashboard.business.show');
    Route::post('/dashboard/business/reviews', [BusinessController::class, 'storeReview'])->name('dashboard.business.reviews.store');

    // Dedicated Business Management Console Route
    Route::get('/dashboard/business', [BusinessController::class, 'index'])->name('dashboard.business.index');
    Route::get('/dashboard/business/create', [BusinessController::class, 'create'])->name('dashboard.business.create');
    Route::get('/dashboard/business/edit', [BusinessController::class, 'edit'])->name('dashboard.business.edit');

    // Business Enterprise & Dependent Modules (Products, Services, Jobs, Razorpay Subscriptions)
    Route::prefix('dashboard/business')->name('dashboard.business.')->group(function () {
        Route::get('subscription', [BusinessController::class, 'selectSubscription'])->name('subscription');
        Route::post('register', [BusinessController::class, 'registerBusiness'])->name('register');
        Route::post('update', [BusinessController::class, 'updateBusiness'])->name('update');
        Route::delete('delete', [BusinessController::class, 'deleteBusiness'])->name('delete');
        Route::post('subscribe', [BusinessController::class, 'createBusinessOrder'])->name('subscribe');
        Route::post('verify-payment', [BusinessController::class, 'verifyBusinessPayment'])->name('verify-payment');

        // Products Catalog CRUD
        Route::post('products', [BusinessController::class, 'addProduct'])->name('products.add');
        Route::delete('products/{id}', [BusinessController::class, 'deleteProduct'])->name('products.delete');

        // Services Catalog CRUD
        Route::post('services', [BusinessController::class, 'addService'])->name('services.add');
        Route::delete('services/{id}', [BusinessController::class, 'deleteService'])->name('services.delete');

        // Jobs Hub CRUD & Applicant tracking
        Route::post('jobs', [BusinessController::class, 'addJob'])->name('jobs.add');
        Route::post('jobs/apply', [BusinessController::class, 'applyJob'])->name('jobs.apply');
        Route::post('jobs/{id}/update', [BusinessController::class, 'updateJob'])->name('jobs.update');
        Route::delete('jobs/{id}', [BusinessController::class, 'deleteJob'])->name('jobs.delete');
        Route::post('jobs/{id}/toggle', [BusinessController::class, 'toggleJob'])->name('jobs.toggle');
        Route::post('applications/{id}/status', [BusinessController::class, 'updateApplicationStatus'])->name('applications.status');
    });

    // ── Matrimony Module ──────────────────────────────────────────────────
    Route::prefix('dashboard/matrimony')->name('matrimony.')->group(function () {
        Route::get('/',                          [\App\Http\Controllers\MatrimonyController::class, 'index'])->name('index');
        Route::get('/create',                    [\App\Http\Controllers\MatrimonyController::class, 'create'])->name('create');
        Route::get('/subscription',              [\App\Http\Controllers\MatrimonyController::class, 'selectSubscription'])->name('subscription');
        Route::post('/create',                   [\App\Http\Controllers\MatrimonyController::class, 'store'])->name('store');
        Route::get('/edit',                      [\App\Http\Controllers\MatrimonyController::class, 'edit'])->name('edit');
        Route::post('/update',                   [\App\Http\Controllers\MatrimonyController::class, 'update'])->name('update');
        Route::delete('/delete',                 [\App\Http\Controllers\MatrimonyController::class, 'destroy'])->name('delete');
        Route::get('/browse',                    [\App\Http\Controllers\MatrimonyController::class, 'browse'])->name('browse');
        Route::get('/profile/{id}',              [\App\Http\Controllers\MatrimonyController::class, 'show'])->name('show');
        Route::post('/request/send',             [\App\Http\Controllers\MatrimonyController::class, 'sendRequest'])->name('request.send');
        Route::get('/requests',                  [\App\Http\Controllers\MatrimonyController::class, 'requests'])->name('requests');
        Route::post('/request/{id}/respond',     [\App\Http\Controllers\MatrimonyController::class, 'respondRequest'])->name('request.respond');
        Route::get('/conversations',             [\App\Http\Controllers\MatrimonyController::class, 'conversations'])->name('conversations');
        Route::get('/chat/{conversationId}',     [\App\Http\Controllers\MatrimonyController::class, 'chat'])->name('chat');
        Route::post('/chat/send',                [\App\Http\Controllers\MatrimonyController::class, 'sendMessage'])->name('chat.send');
        Route::get('/chat/{conversationId}/fetch', [\App\Http\Controllers\MatrimonyController::class, 'fetchMessages'])->name('chat.fetch');
        Route::post('/subscribe',                [\App\Http\Controllers\MatrimonyController::class, 'createOrder'])->name('subscribe');
        Route::post('/verify-payment',           [\App\Http\Controllers\MatrimonyController::class, 'verifyPayment'])->name('verify-payment');
    });

    // ── Subscription Module ───────────────────────────────────────────────
    Route::prefix('dashboard/subscriptions')->name('subscriptions.')->group(function () {
        Route::get('/', [SubscriptionController::class, 'index'])->name('index');
        Route::get('/{id}', [SubscriptionController::class, 'show'])->name('show');
        Route::get('/{id}/invoice', [SubscriptionController::class, 'invoice'])->name('invoice');
    });

    // ── Pages & Applied Jobs Utilities ────────────────────────────────────
    Route::get('/dashboard/privacy-policy', [DashboardController::class, 'privacyPolicy'])->name('dashboard.privacy-policy');
    Route::get('/dashboard/terms-conditions', [DashboardController::class, 'termsConditions'])->name('dashboard.terms-conditions');
    Route::get('/dashboard/contact-support', [DashboardController::class, 'contactSupport'])->name('dashboard.contact-support');
    Route::get('/dashboard/jobs/applied', [DashboardController::class, 'appliedJobs'])->name('dashboard.jobs.applied');

    // ── Donation Intent Checkout ──────────────────────────────────────────
    Route::get('/dashboard/donations/suggest', [\App\Http\Controllers\DonationController::class, 'suggestCause'])->name('dashboard.donations.suggest');
    Route::post('/dashboard/donations/checkout', [\App\Http\Controllers\DonationController::class, 'createOrder'])->name('dashboard.donations.checkout');
    Route::post('/dashboard/donations/verify', [\App\Http\Controllers\DonationController::class, 'verifyPayment'])->name('dashboard.donations.verify');

    // ── Volunteers Module ─────────────────────────────────────────────────
    Route::prefix('dashboard/volunteers')->name('volunteers.')->group(function () {
        Route::get('/', [VolunteerController::class, 'index'])->name('index');
        Route::post('/profile', [VolunteerController::class, 'updateProfile'])->name('profile.update');
        Route::get('/browse', [VolunteerController::class, 'browse'])->name('browse');
        Route::get('/opportunity/{id}', [VolunteerController::class, 'show'])->name('opportunity.show');
        Route::post('/opportunity/apply', [VolunteerController::class, 'apply'])->name('opportunity.apply');
        Route::post('/application/{id}/withdraw', [VolunteerController::class, 'withdraw'])->name('application.withdraw');
    });

    // ── Blog Module ───────────────────────────────────────────────────────
    Route::prefix('dashboard/blogs')->name('blogs.')->group(function () {
        Route::get('/', [\App\Http\Controllers\BlogController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\BlogController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\BlogController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\BlogController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [\App\Http\Controllers\BlogController::class, 'edit'])->name('edit');
        Route::post('/{id}/update', [\App\Http\Controllers\BlogController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\BlogController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/like', [\App\Http\Controllers\BlogController::class, 'like'])->name('like');
        Route::post('/{blogId}/comments', [\App\Http\Controllers\BlogCommentController::class, 'store'])->name('comments.store');
        Route::delete('/comments/{id}', [\App\Http\Controllers\BlogCommentController::class, 'destroy'])->name('comments.destroy');
    });
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
            Route::get('/create', [UserManagementController::class, 'create'])->name('create');
            Route::post('/', [UserManagementController::class, 'store'])->name('store');
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
            Route::get('/{id}/edit', [CategoryManagementController::class, 'edit'])->name('edit');
            Route::post('/{id}/update', [CategoryManagementController::class, 'update'])->name('update');
        });

        // Business Management Routes
        Route::prefix('businesses')->name('businesses.')->group(function () {
            Route::get('/', [BusinessManagementController::class, 'index'])->name('index');
            Route::get('/pending', [BusinessManagementController::class, 'pending'])->name('pending');
            Route::get('/verification', [BusinessManagementController::class, 'verification'])->name('verification');
            Route::get('/create', [BusinessManagementController::class, 'create'])->name('create');
            Route::post('/', [BusinessManagementController::class, 'store'])->name('store');
            Route::post('/{id}/approve', [BusinessManagementController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [BusinessManagementController::class, 'reject'])->name('reject');
            Route::get('/{id}/jobs', [BusinessManagementController::class, 'jobs'])->name('jobs');
            Route::post('/jobs/{jobId}/approve', [BusinessManagementController::class, 'approveJob'])->name('jobs.approve');
            Route::post('/jobs/{jobId}/reject', [BusinessManagementController::class, 'rejectJob'])->name('jobs.reject');
            Route::get('/{id}', [BusinessManagementController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [BusinessManagementController::class, 'edit'])->name('edit');
            Route::put('/{id}', [BusinessManagementController::class, 'update'])->name('update');
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
            Route::get('/create', [MatrimonyManagementController::class, 'create'])->name('create');
            Route::post('/', [MatrimonyManagementController::class, 'store'])->name('store');
            Route::post('/{id}/approve', [MatrimonyManagementController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [MatrimonyManagementController::class, 'reject'])->name('reject');
            Route::get('/{id}/edit', [MatrimonyManagementController::class, 'edit'])->name('edit');
            Route::put('/{id}', [MatrimonyManagementController::class, 'update'])->name('update');
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
            Route::get('/export', [PaymentManagementController::class, 'export'])->name('export');
            Route::get('/export-xlsx', [PaymentManagementController::class, 'exportXlsx'])->name('exportXlsx');
            Route::get('/business', [PaymentManagementController::class, 'businessTransactions'])->name('business');
            Route::get('/business/export', [PaymentManagementController::class, 'exportBusiness'])->name('exportBusiness');
            Route::get('/business/export-xlsx', [PaymentManagementController::class, 'exportBusinessXlsx'])->name('exportBusinessXlsx');
            Route::get('/matrimony', [PaymentManagementController::class, 'matrimonyTransactions'])->name('matrimony');
            Route::get('/matrimony/export', [PaymentManagementController::class, 'exportMatrimony'])->name('exportMatrimony');
            Route::get('/matrimony/export-xlsx', [PaymentManagementController::class, 'exportMatrimonyXlsx'])->name('exportMatrimonyXlsx');
            Route::get('/transaction/{id}', [PaymentManagementController::class, 'showTransaction'])->name('transaction.show');
            Route::get('/{id}', [PaymentManagementController::class, 'show'])->name('show');
            Route::post('/{id}/refund', [PaymentManagementController::class, 'refund'])->name('refund');
            Route::post('/{id}/update-status', [PaymentManagementController::class, 'updateStatus'])->name('update-status');
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

        // Blog Management Routes
        Route::prefix('blogs')->name('blogs.')->group(function () {
            Route::get('/', [BlogManagementController::class, 'index'])->name('index');
            Route::get('/access', [BlogManagementController::class, 'accessUsers'])->name('access');
            Route::patch('/users/{id}/toggle-access', [BlogManagementController::class, 'toggleUserAccess'])->name('toggle-user-access');
            Route::get('/create', [BlogManagementController::class, 'create'])->name('create');
            Route::post('/', [BlogManagementController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [BlogManagementController::class, 'edit'])->name('edit');
            Route::put('/{id}', [BlogManagementController::class, 'update'])->name('update');
            Route::delete('/{id}', [BlogManagementController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/toggle-status', [BlogManagementController::class, 'toggleStatus'])->name('toggle-status');
            Route::delete('/comments/{id}', [BlogManagementController::class, 'deleteComment'])->name('comments.destroy');
        });

        // Blog Category Management Routes
        Route::prefix('blog-categories')->name('blog-categories.')->group(function () {
            Route::get('/', [BlogCategoryManagementController::class, 'index'])->name('index');
            Route::get('/create', [BlogCategoryManagementController::class, 'create'])->name('create');
            Route::post('/', [BlogCategoryManagementController::class, 'store'])->name('store');
            Route::get('/{id}', [BlogCategoryManagementController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [BlogCategoryManagementController::class, 'edit'])->name('edit');
            Route::put('/{id}', [BlogCategoryManagementController::class, 'update'])->name('update');
            Route::post('/{id}/toggle-status', [BlogCategoryManagementController::class, 'toggleStatus'])->name('toggle-status');
            Route::delete('/{id}', [BlogCategoryManagementController::class, 'destroy'])->name('destroy');
        });

        // Blogger Management Routes
        Route::prefix('bloggers')->name('bloggers.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\BloggerManagementController::class, 'index'])->name('index');
            Route::post('/check-email', [\App\Http\Controllers\Admin\BloggerManagementController::class, 'checkEmail'])->name('check-email');
            Route::get('/create', [\App\Http\Controllers\Admin\BloggerManagementController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\BloggerManagementController::class, 'store'])->name('store');
            Route::get('/{id}', [\App\Http\Controllers\Admin\BloggerManagementController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [\App\Http\Controllers\Admin\BloggerManagementController::class, 'edit'])->name('edit');
            Route::put('/{id}', [\App\Http\Controllers\Admin\BloggerManagementController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\BloggerManagementController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/toggle-status', [\App\Http\Controllers\Admin\BloggerManagementController::class, 'toggleStatus'])->name('toggle-status');
        });
        
        // Education Management Routes
        Route::prefix('educations')->name('educations.')->group(function () {
            Route::get('/', [EducationManagementController::class, 'index'])->name('index');
            Route::get('/create', [EducationManagementController::class, 'create'])->name('create');
            Route::post('/', [EducationManagementController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [EducationManagementController::class, 'edit'])->name('edit');
            Route::put('/{id}', [EducationManagementController::class, 'update'])->name('update');
            Route::delete('/{id}', [EducationManagementController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/toggle-status', [EducationManagementController::class, 'toggleStatus'])->name('toggle-status');
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

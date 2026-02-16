<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\BusinessController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\MatrimonyController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\VolunteerController;
use App\Http\Controllers\Api\DonationController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\SocialAuthController;
use App\Http\Controllers\Api\HomepageHeroController;
use App\Http\Controllers\Api\GoogleAuthController;
use App\Http\Controllers\Api\PlanController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public authentication routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    
    Route::post('google-login', [GoogleAuthController::class, 'login']);

    Route::get('google', [SocialAuthController::class, 'redirectToGoogle']);
    Route::get('google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

    Route::get('facebook', [SocialAuthController::class, 'redirectToFacebook']);
    Route::get('facebook/callback', [SocialAuthController::class, 'handleFacebookCallback']);
    
    // Forgot password (OTP) APIs
    Route::post('password/forgot', [PasswordResetController::class, 'sendOtp']);
    Route::post('password/verify-otp', [PasswordResetController::class, 'verifyOtp']);
    Route::post('password/reset', [PasswordResetController::class, 'resetPassword']);
});

// Volunteer Routes
    Route::prefix('banner')->group(function () {
        Route::get('/', [HomepageHeroController::class, 'index']);   // list (paginated)
        Route::get('/{hero}', [HomepageHeroController::class, 'show']); // single
    });

// Public business routes (for browsing)
Route::prefix('business')->group(function () {
    Route::get('/', [BusinessController::class, 'index']);
    Route::get('/{id}', [BusinessController::class, 'show']);
    Route::get('/category/{id}', [BusinessController::class, 'showOnCategory']);
    Route::get('/{id}/products', [BusinessController::class, 'getProducts']);
    Route::get('/{id}/services', [BusinessController::class, 'getServices']);
    Route::post('/search_business', [BusinessController::class, 'searchBusiness']);
});

// Public matrimony routes (for browsing)
Route::prefix('matrimony')->group(function () {
    Route::get('/search', [MatrimonyController::class, 'searchProfiles']);
    // Route::get('/profile/{id}', [MatrimonyController::class, 'showProfile']);
    Route::middleware('auth:sanctum')->get(
    '/profile/{id}',
    [MatrimonyController::class, 'showProfile']);

    // Cast and SubCast routes
    Route::get('/casts', [MatrimonyController::class, 'getCasts']);
    Route::get('/casts/{castId}/subcasts', [MatrimonyController::class, 'getSubCasts']);

});

// Public volunteer routes (for browsing)
Route::prefix('volunteer')->group(function () {
    Route::get('/opportunities', [VolunteerController::class, 'getOpportunities']);
    Route::get('/opportunity/{id}', [VolunteerController::class, 'getOpportunity']);
});

// Public donation routes (for browsing)
Route::prefix('donation')->group(function () {
    Route::get('/causes', [DonationController::class, 'getCauses']);
    Route::get('/cause/{id}', [DonationController::class, 'getCause']);
});

// Public plans routes
Route::prefix('plans')->group(function () {
    Route::get('business', [PlanController::class, 'businessPlans']);
    Route::get('matrimony', [PlanController::class, 'matrimonyPlans']);
});

// Public job routes (for browsing)
Route::prefix('jobs')->group(function () {
    Route::get('/', [JobController::class, 'index']);
    Route::get('/{id}', [JobController::class, 'show']);
});

// Payment webhook (public)
Route::post('payment/webhook', [PaymentController::class, 'webhook']);

// Public search routes
Route::prefix('search')->group(function () {
    Route::get('global', [SearchController::class, 'globalSearch']);
    Route::get('businesses', [SearchController::class, 'searchBusinesses']);
    // Route::post('matrimony', [SearchController::class, 'searchMatrimony']);
    
    Route::middleware('auth:sanctum')->post(
    'matrimony',
    [SearchController::class, 'searchMatrimony']);
    
    Route::get('jobs', [SearchController::class, 'searchJobs']);
    Route::get('volunteers', [SearchController::class, 'searchVolunteers']);
    Route::get('donations', [SearchController::class, 'searchDonations']);
    Route::get('suggestions', [SearchController::class, 'getSuggestions']);
    Route::get('location', [SearchController::class, 'locationSearch']);
});

// Protected authentication routes
Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('profile', [AuthController::class, 'profile']);
    Route::put('profile', [AuthController::class, 'updateProfile']);
    Route::post('change-password', [AuthController::class, 'changePassword']);
});

// Test route to verify authentication
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json([
        'success' => true,
        'data' => ['user' => $request->user()]
    ]);
});

// Protected routes for authenticated users
Route::middleware(['auth:sanctum'])->group(function () {
    
    // Business Management Routes
    Route::prefix('business')->group(function () {
        Route::post('register', [BusinessController::class, 'register']);
        Route::post('my-businesses', [BusinessController::class, 'getUserBusinesses']);
        Route::put('{id}', [BusinessController::class, 'update']);
        Route::delete('{id}', [BusinessController::class, 'destroy']);
        Route::post('products', [BusinessController::class, 'addProduct']);
        Route::post('services', [BusinessController::class, 'addService']);
    });

    // Business Management Routes
    Route::prefix('category')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::post('register', [CategoryController::class, 'register']);
        Route::get('/{id}', [CategoryController::class, 'show']);
        Route::put('{id}', [CategoryController::class, 'update']);
        Route::delete('{id}', [CategoryController::class, 'destroy']);
    });
    
    // Matrimony Routes
    Route::prefix('matrimony')->group(function () {
        Route::post('profile', [MatrimonyController::class, 'createProfile']);
        Route::get('profile', [MatrimonyController::class, 'getProfile']);
        Route::put('profile', [MatrimonyController::class, 'updateProfile']);
        
        // Connection requests
        Route::post('connection-request', [MatrimonyController::class, 'sendConnectionRequest']);
        Route::post('remove-request', [MatrimonyController::class, 'sendRemoveUser']);
        Route::put('connection-request/{id}', [MatrimonyController::class, 'respondToConnectionRequest']);
        Route::get('connection-requests', [MatrimonyController::class, 'getConnectionRequests']);
        Route::get('connected-users', [MatrimonyController::class, 'getConnectedUsers']);
        
        // Chat system
        Route::get('conversations', [MatrimonyController::class, 'getConversations']);
        Route::get('messages/{conversationId}', [MatrimonyController::class, 'getMessages']);
        Route::post('send-message', [MatrimonyController::class, 'sendMessage']);
    });
    
    // Payment Routes
    Route::prefix('payment')->group(function () {
        Route::post('create-order', [PaymentController::class, 'createOrder']);
        Route::post('verify', [PaymentController::class, 'verifyPayment']);
        Route::post('transactions', [PaymentController::class, 'getTransactions']);
        Route::post('transaction/{id}', [PaymentController::class, 'getTransaction']);
        Route::post('refund', [PaymentController::class, 'initiateRefund']);
    });
    
    // Notification Routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('unread-count', [NotificationController::class, 'getUnreadCount']);
        Route::get('statistics', [NotificationController::class, 'getStatistics']);
        Route::get('preferences', [NotificationController::class, 'getPreferences']);
        Route::put('preferences', [NotificationController::class, 'updatePreferences']);
        Route::put('{id}/read', [NotificationController::class, 'markAsRead']);
        Route::put('{id}/unread', [NotificationController::class, 'markAsUnread']);
        Route::put('mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::put('mark-multiple-read', [NotificationController::class, 'markMultipleAsRead']);
        Route::delete('{id}', [NotificationController::class, 'destroy']);
        Route::delete('/', [NotificationController::class, 'destroyMultiple']);
    });
    
    // Volunteer Routes
    Route::prefix('volunteer')->group(function () {
        // Volunteer profile management
        Route::get('profile', [VolunteerController::class, 'getVolunteerProfile']);
        Route::post('profile', [VolunteerController::class, 'updateVolunteerProfile']);
        Route::put('profile', [VolunteerController::class, 'updateVolunteerProfile']);
        
        // Volunteer opportunities management
        Route::post('opportunities', [VolunteerController::class, 'createOpportunity']);
        Route::put('opportunities/{id}', [VolunteerController::class, 'updateOpportunity']);
        Route::delete('opportunities/{id}', [VolunteerController::class, 'deleteOpportunity']);
        
        // Volunteer applications
        Route::post('apply', [VolunteerController::class, 'applyForOpportunity']);
        Route::get('applications', [VolunteerController::class, 'getMyApplications']);
        Route::put('applications/{id}/withdraw', [VolunteerController::class, 'withdrawApplication']);
        
        // Matching and recommendations
        Route::get('matched-opportunities', [VolunteerController::class, 'getMatchedOpportunities']);
    });
    
    // Donation Routes
    Route::prefix('donation')->group(function () {
        // Donation management
        Route::post('create-order', [DonationController::class, 'createDonationOrder']);
        Route::post('verify-payment', [DonationController::class, 'verifyDonationPayment']);
        
        // Donation history and analytics
        Route::get('history', [DonationController::class, 'getDonationHistory']);
        Route::get('analytics', [DonationController::class, 'getDonationAnalytics']);
        Route::get('receipt/{donationId}', [DonationController::class, 'downloadReceipt']);
    });
    
    // Job Management Routes
    Route::prefix('jobs')->group(function () {
        // Job posting management (Business users)
        Route::post('/', [JobController::class, 'store']);
        Route::put('/{id}', [JobController::class, 'update']);
        Route::delete('/{id}', [JobController::class, 'destroy']);
        Route::post('/my-jobs', [JobController::class, 'getUserJobs']);
        Route::post('/{id}/toggle-status', [JobController::class, 'toggleStatus']);
        
        // Job application management
        Route::post('/apply', [JobController::class, 'apply']);
        Route::post('/my-applications', [JobController::class, 'getUserApplications']);
        Route::get('/{jobId}/applications', [JobController::class, 'getJobApplications']);
        Route::put('/applications/{applicationId}/status', [JobController::class, 'updateApplicationStatus']);
        
        // Job analytics
        Route::post('/analytics', [JobController::class, 'getJobAnalytics']);
    });
    
    // Admin Routes
    Route::prefix('admin')->group(function () {
        Route::get('dashboard', [AdminController::class, 'getDashboardStats']);
        Route::get('analytics', [AdminController::class, 'getAnalytics']);
        
        // User management
        Route::get('users', [AdminController::class, 'getUsers']);
        Route::post('users/bulk-operation', [AdminController::class, 'bulkUserOperation']);
        Route::post('users/{id}/suspend', [AdminController::class, 'suspendUser']);
        Route::post('create-admin', [AdminController::class, 'createAdmin']);
        
        // Caste certificate verification
        Route::get('pending-verifications', [AdminController::class, 'getPendingVerifications']);
        Route::put('verify-certificate/{id}', [AdminController::class, 'verifyCasteCertificate']);
        
        // Business management
        Route::get('businesses', [AdminController::class, 'getBusinesses']);
        Route::put('verify-business/{id}', [AdminController::class, 'verifyBusiness']);
        
        // Matrimony moderation
        Route::get('matrimony-profiles', [AdminController::class, 'getMatrimonyProfiles']);
        Route::put('moderate-profile/{id}', [AdminController::class, 'moderateMatrimonyProfile']);
        
        // Transaction management
        Route::get('transactions', [AdminController::class, 'getTransactions']);
        Route::get('payment-stats', [PaymentController::class, 'getPaymentStats']);
        
        // System settings
        Route::get('settings', [AdminController::class, 'getSystemSettings']);
        Route::put('settings', [AdminController::class, 'updateSystemSettings']);
        
        // Volunteer management
        Route::get('volunteer/opportunities', [AdminController::class, 'getVolunteerOpportunities']);
        Route::put('volunteer/opportunities/{id}', [AdminController::class, 'manageVolunteerOpportunity']);
        Route::post('volunteer/opportunities/bulk-operation', [AdminController::class, 'bulkVolunteerOpportunityOperation']);
        Route::get('volunteer/applications', [AdminController::class, 'getVolunteerApplications']);
        Route::put('volunteer/applications/{id}', [AdminController::class, 'manageVolunteerApplication']);
        Route::get('volunteer/analytics', [AdminController::class, 'getVolunteerAnalytics']);
        
        // Donation management
        Route::get('donations/causes', [AdminController::class, 'getDonationCauses']);
        Route::post('donations/causes', [AdminController::class, 'createDonationCause']);
        Route::put('donations/causes/{id}', [AdminController::class, 'updateDonationCause']);
        Route::delete('donations/causes/{id}', [AdminController::class, 'deleteDonationCause']);
        Route::get('donations/analytics', [AdminController::class, 'getDonationAnalytics']);
        Route::get('donations/transactions', [AdminController::class, 'getDonationTransactions']);
        
        // Job management
         Route::get('jobs', [AdminController::class, 'getJobPostings']);
         Route::put('jobs/{id}/approve', [AdminController::class, 'approveJobPosting']);
         Route::put('jobs/{id}/reject', [AdminController::class, 'rejectJobPosting']);
         Route::get('jobs/analytics', [AdminController::class, 'getJobAnalytics']);
         Route::get('jobs/applications', [AdminController::class, 'getJobApplications']);
         Route::post('jobs/bulk-operation', [AdminController::class, 'bulkJobOperation']);
    });
});

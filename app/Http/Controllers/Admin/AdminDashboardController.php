<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Business;
use App\Models\MatrimonyProfile;
use App\Models\Transaction;
use App\Models\ConnectionRequest;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function index()
    {
        // Get dashboard statistics
        $stats = $this->getDashboardStats();
        
        // Get recent activities
        $recentUsers = User::latest()->take(5)->get();
        $recentBusinesses = Business::with('user')->latest()->take(5)->get();
        $recentTransactions = Transaction::with('user')->where('status', 'completed')->latest()->take(5)->get();
        
        // Get pending verifications count
        $pendingVerifications = [
            'caste_certificates' => User::where('caste_verification_status', 'pending')->count(),
            'businesses' => Business::where('verification_status', 'pending')->count(),
            'matrimony_profiles' => MatrimonyProfile::where('approval_status', 'pending')->count(),
        ];
        
        return view('admin.dashboard.index', compact(
            'stats', 
            'recentUsers', 
            'recentBusinesses', 
            'recentTransactions',
            'pendingVerifications'
        ));
    }
    
    /**
     * Show analytics page
     */
    public function analytics(Request $request)
    {
        $period = $request->get('period', 30); // Default 30 days
        $startDate = Carbon::now()->subDays($period);
        
        // User registration trends
        $userRegistrations = User::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Revenue trends
        $revenueTrends = Transaction::where('created_at', '>=', $startDate)
            ->where('status', 'completed')
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Business registrations
        $businessRegistrations = Business::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Matrimony profiles
        $matrimonyProfiles = MatrimonyProfile::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // User type distribution
        $userTypeDistribution = User::select('user_type', DB::raw('count(*) as count'))
            ->groupBy('user_type')
            ->get();
        
        // Payment purpose distribution
        $paymentDistribution = Transaction::where('status', 'completed')
            ->select('purpose', DB::raw('SUM(amount) as total'))
            ->groupBy('purpose')
            ->get();
        
        return view('admin.dashboard.analytics', compact(
            'userRegistrations',
            'revenueTrends', 
            'businessRegistrations',
            'matrimonyProfiles',
            'userTypeDistribution',
            'paymentDistribution',
            'period'
        ));
    }
    
    /**
     * Show settings page
     */
    public function settings()
    {
        // Get current settings (you might want to create a settings table)
        $settings = [
            'business_registration_fee' => 500.00,
            'matrimony_profile_fee' => 300.00,
            'trial_period_days' => 7,
            'max_job_postings' => 5,
            'app_name' => 'Mali Setu',
            'support_email' => 'support@malisetu.com',
            'razorpay_key_id' => env('RAZORPAY_KEY_ID'),
            'maintenance_mode' => false,
        ];
        
        return view('admin.dashboard.settings', compact('settings'));
    }
    
    /**
     * Update system settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'business_registration_fee' => 'nullable|numeric|min:0',
            'matrimony_profile_fee' => 'nullable|numeric|min:0',
            'trial_period_days' => 'nullable|integer|min:0',
            'max_job_postings' => 'nullable|integer|min:0',
            'app_name' => 'nullable|string|max:255',
            'support_email' => 'nullable|email',
            'maintenance_mode' => 'nullable|boolean',
        ]);
        
        // Update settings in database (implement settings table)
        // For now, just return success message
        
        return back()->with('success', 'Settings updated successfully!');
    }
    
    /**
     * Clear application cache
     */
    public function clearCache()
    {
        try {
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('route:clear');
            \Artisan::call('view:clear');
            
            return redirect()->back()->with('success', 'Cache cleared successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }
    
    /**
     * Run database migrations
     */
    public function runMigrations()
    {
        try {
            \Artisan::call('migrate', ['--force' => true]);
            
            return redirect()->back()->with('success', 'Migrations executed successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to run migrations: ' . $e->getMessage());
        }
    }
    
    /**
     * Optimize application
     */
    public function optimizeApp()
    {
        try {
            \Artisan::call('optimize');
            \Artisan::call('config:cache');
            \Artisan::call('route:cache');
            \Artisan::call('view:cache');
            
            return redirect()->back()->with('success', 'Application optimized successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to optimize application: ' . $e->getMessage());
        }
    }
    
    /**
     * Clear logs
     */
    public function clearLogs()
    {
        try {
            $logPath = storage_path('logs/laravel.log');
            if (file_exists($logPath)) {
                file_put_contents($logPath, '');
            }
            
            return redirect()->back()->with('success', 'Logs cleared successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to clear logs: ' . $e->getMessage());
        }
    }
    
    /**
     * Generate application key
     */
    public function generateKey()
    {
        try {
            \Artisan::call('key:generate', ['--force' => true]);
            
            return redirect()->back()->with('success', 'Application key generated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate key: ' . $e->getMessage());
        }
    }
    
    /**
     * Create storage link
     */
    public function createStorageLink()
    {
        try {
            \Artisan::call('storage:link');
            
            return redirect()->back()->with('success', 'Storage link created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create storage link: ' . $e->getMessage());
        }
    }
    
    /**
     * Backup database
     */
    public function backupDatabase()
    {
        try {
            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $backupPath = storage_path('app/backups/' . $filename);
            
            // Create backups directory if it doesn't exist
            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }
            
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');
            $dbHost = config('database.connections.mysql.host');
            
            $command = "mysqldump -h {$dbHost} -u {$dbUser} -p{$dbPass} {$dbName} > {$backupPath}";
            
            exec($command, $output, $returnVar);
            
            if ($returnVar === 0) {
                return redirect()->back()->with('success', 'Database backup created: ' . $filename);
            } else {
                return redirect()->back()->with('error', 'Failed to create database backup.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to backup database: ' . $e->getMessage());
        }
    }
    
    /**
     * Get system information
     */
    public function getSystemInfo()
    {
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database_version' => \DB::select('SELECT VERSION() as version')[0]->version ?? 'Unknown',
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'disk_free_space' => $this->formatBytes(disk_free_space('/')),
            'disk_total_space' => $this->formatBytes(disk_total_space('/'))
        ];
        
        return response()->json($systemInfo);
    }
    
    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    /**
     * Get dashboard statistics
     */
    private function getDashboardStats()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        
        return [
            'users' => [
                'total' => User::count(),
                'verified' => User::where('caste_verification_status', 'approved')->count(),
                'pending' => User::where('caste_verification_status', 'pending')->count(),
                'today' => User::whereDate('created_at', $today)->count(),
                'this_month' => User::where('created_at', '>=', $thisMonth)->count(),
                'by_type' => User::select('user_type', DB::raw('count(*) as count'))
                    ->groupBy('user_type')->get()->pluck('count', 'user_type'),
            ],
            'businesses' => [
                'total' => Business::count(),
                'approved' => Business::where('verification_status', 'approved')->count(),
                'pending' => Business::where('verification_status', 'pending')->count(),
                'active_subscriptions' => Business::where('subscription_status', 'active')->count(),
                'today' => Business::whereDate('created_at', $today)->count(),
            ],
            'matrimony' => [
                'total' => MatrimonyProfile::count(),
                'approved' => MatrimonyProfile::where('approval_status', 'approved')->count(),
                'pending' => MatrimonyProfile::where('approval_status', 'pending')->count(),
                'connections' => ConnectionRequest::where('status', 'accepted')->count(),
                'today' => MatrimonyProfile::whereDate('created_at', $today)->count(),
            ],
            'payments' => [
                'total_revenue' => Transaction::where('status', 'completed')->sum('amount'),
                'total_transactions' => Transaction::where('status', 'completed')->count(),
                'pending_payments' => Transaction::where('status', 'pending')->count(),
                'today_revenue' => Transaction::where('status', 'completed')
                    ->whereDate('created_at', $today)->sum('amount'),
                'monthly_revenue' => Transaction::where('status', 'completed')
                    ->where('created_at', '>=', $thisMonth)->sum('amount'),
            ],
            'activity' => [
                'messages_today' => ChatMessage::whereDate('created_at', $today)->count(),
                'new_registrations_today' => User::whereDate('created_at', $today)->count(),
                'new_businesses_today' => Business::whereDate('created_at', $today)->count(),
                'active_conversations' => ChatMessage::whereDate('created_at', $today)
                    ->distinct('conversation_id')->count('conversation_id'),
            ]
        ];
    }
}
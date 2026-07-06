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

        // Fetch subscription expiry reminders (logic from SubscriptionReminderService)
        $today = Carbon::now()->startOfDay();
        $reminderStages = [
            'before_3_months' => -3,
            'before_2_months' => -2,
            'before_1_month'  => -1,
            'on_date'         => 0,
            'after_1_month'   => 1,
            'after_2_months'  => 2,
            'after_3_months'  => 3,
            'after_4_months'  => 4,
            'after_5_months'  => 5,
            'after_6_months'  => 6,
        ];
        
        $earliest = $today->copy()->subMonthsNoOverflow(6)->startOfDay();
        $latest = $today->copy()->addMonthsNoOverflow(3)->endOfDay();
        
        $expiryReminders = [];

        // 1. Process Business Expiries
        $businesses = Business::with('user')
            ->whereNotNull('subscription_expires_at')
            ->whereBetween('subscription_expires_at', [$earliest, $latest])
            ->get();
            
        foreach ($businesses as $business) {
            if (!$business->user) {
                continue;
            }
            
            $expiry = Carbon::parse($business->subscription_expires_at)->startOfDay();
            
            foreach ($reminderStages as $stageKey => $monthDelta) {
                $sendAt = $expiry->copy()->addMonthsNoOverflow($monthDelta);
                
                if ($sendAt->isSameDay($today)) {
                    $stageLabels = [
                        'before_3_months' => '3 months before expiry',
                        'before_2_months' => '2 months before expiry',
                        'before_1_month'  => '1 month before expiry',
                        'on_date'         => 'on expiry date',
                        'after_1_month'   => '1 month after expiry',
                        'after_2_months'  => '2 months after expiry',
                        'after_3_months'  => '3 months after expiry',
                        'after_4_months'  => '4 months after expiry',
                        'after_5_months'  => '5 months after expiry',
                        'after_6_months'  => '6 months after expiry',
                    ];
                    
                    $user = $business->user;
                    $phone = preg_replace('/[^0-9]/', '', $user->phone ?? '');
                    if (strlen($phone) === 10) {
                        $phone = '91' . $phone;
                    }
                    
                    $expiryDateStr = $expiry->format('M d, Y');
                    if (str_starts_with($stageKey, 'before_')) {
                        $msg = "Hello {$user->name}, your subscription for \"{$business->business_name}\" will expire on {$expiryDateStr}. Please renew to avoid any interruption.";
                    } elseif ($stageKey === 'on_date') {
                        $msg = "Hello {$user->name}, your subscription for \"{$business->business_name}\" expires today ({$expiryDateStr}). Please renew to keep your listing active.";
                    } else {
                        $msg = "Hello {$user->name}, your subscription for \"{$business->business_name}\" expired on {$expiryDateStr}. Please renew to continue enjoying the service.";
                    }
                    
                    $waLink = !empty($phone) ? "https://wa.me/{$phone}?text=" . urlencode($msg) : '#';
                    
                    $expiryReminders[] = [
                        'type' => 'Business',
                        'entity_id' => $business->id,
                        'entity_name' => $business->business_name,
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'user_phone' => $user->phone ?? 'N/A',
                        'expiry_date' => $expiry->format('Y-m-d'),
                        'stage_label' => $stageLabels[$stageKey],
                        'wa_link' => $waLink
                    ];
                    break;
                }
            }
        }

        // 2. Process Matrimony Profile Expiries
        $matrimonyProfiles = MatrimonyProfile::with('user')
            ->whereNotNull('profile_expires_at')
            ->whereBetween('profile_expires_at', [$earliest, $latest])
            ->get();

        foreach ($matrimonyProfiles as $profile) {
            if (!$profile->user) {
                continue;
            }
            
            $expiry = Carbon::parse($profile->profile_expires_at)->startOfDay();
            
            foreach ($reminderStages as $stageKey => $monthDelta) {
                $sendAt = $expiry->copy()->addMonthsNoOverflow($monthDelta);
                
                if ($sendAt->isSameDay($today)) {
                    $stageLabels = [
                        'before_3_months' => '3 months before expiry',
                        'before_2_months' => '2 months before expiry',
                        'before_1_month'  => '1 month before expiry',
                        'on_date'         => 'on expiry date',
                        'after_1_month'   => '1 month after expiry',
                        'after_2_months'  => '2 months after expiry',
                        'after_3_months'  => '3 months after expiry',
                        'after_4_months'  => '4 months after expiry',
                        'after_5_months'  => '5 months after expiry',
                        'after_6_months'  => '6 months after expiry',
                    ];
                    
                    $user = $profile->user;
                    $phone = preg_replace('/[^0-9]/', '', $user->phone ?? '');
                    if (strlen($phone) === 10) {
                        $phone = '91' . $phone;
                    }
                    
                    $expiryDateStr = $expiry->format('M d, Y');
                    if (str_starts_with($stageKey, 'before_')) {
                        $msg = "Hello {$user->name}, your matrimony profile subscription will expire on {$expiryDateStr}. Please renew to avoid any interruption.";
                    } elseif ($stageKey === 'on_date') {
                        $msg = "Hello {$user->name}, your matrimony profile subscription expires today ({$expiryDateStr}). Please renew to keep your profile active.";
                    } else {
                        $msg = "Hello {$user->name}, your matrimony profile subscription expired on {$expiryDateStr}. Please renew to continue enjoying the service.";
                    }
                    
                    $waLink = !empty($phone) ? "https://wa.me/{$phone}?text=" . urlencode($msg) : '#';
                    
                    $expiryReminders[] = [
                        'type' => 'Matrimony',
                        'entity_id' => $profile->id,
                        'entity_name' => 'Matrimony Profile',
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'user_phone' => $user->phone ?? 'N/A',
                        'expiry_date' => $expiry->format('Y-m-d'),
                        'stage_label' => $stageLabels[$stageKey],
                        'wa_link' => $waLink
                    ];
                    break;
                }
            }
        }

        // Sort by expiry date ascending
        usort($expiryReminders, function($a, $b) {
            return strcmp($a['expiry_date'], $b['expiry_date']);
        });
        
        return view('admin.dashboard.index', compact(
            'stats', 
            'recentUsers', 
            'recentBusinesses', 
            'recentTransactions',
            'pendingVerifications',
            'expiryReminders'
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

    /**
     * Show reports list view
     */
    public function reports()
    {
        return view('admin.reports');
    }

    /**
     * Download list report as PDF
     */
    public function downloadReportPdf(Request $request, $type)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        list($title, $headers, $rows, $summary) = $this->getReportData($type, $startDate, $endDate);

        $originalTitle = $title;
        if ($startDate && $endDate) {
            $title .= " (" . date('Y-m-d', strtotime($startDate)) . " to " . date('Y-m-d', strtotime($endDate)) . ")";
        } elseif ($startDate) {
            $title .= " (From " . date('Y-m-d', strtotime($startDate)) . ")";
        } elseif ($endDate) {
            $title .= " (Up to " . date('Y-m-d', strtotime($endDate)) . ")";
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.pdf.report_template', compact('title', 'headers', 'rows', 'summary'));
        
        // Save PDF to a temporary file
        $tempPdfPath = tempnam(sys_get_temp_dir(), 'pdf_report');
        file_put_contents($tempPdfPath, $pdf->output());

        // Create password-protected ZIP archive
        $password = date('dmY');
        $tempZipPath = tempnam(sys_get_temp_dir(), 'zip_report');
        $zip = new \ZipArchive();
        if ($zip->open($tempZipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            $zip->setPassword($password);
            
            // PDF file name inside the zip
            $pdfFileName = strtolower(str_replace(' ', '_', $originalTitle)) . '_' . date('Ymd') . '.pdf';
            
            $zip->addFile($tempPdfPath, $pdfFileName);
            if (defined('ZipArchive::EM_AES_256')) {
                $zip->setEncryptionName($pdfFileName, \ZipArchive::EM_AES_256);
            } else {
                $zip->setEncryptionName($pdfFileName, \ZipArchive::EM_TRAD_PKWARE);
            }
            $zip->close();
        }
        
        // Delete temporary PDF file
        @unlink($tempPdfPath);

        return response()->download($tempZipPath, strtolower(str_replace(' ', '_', $originalTitle)) . '_' . date('Ymd') . '.zip', [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Download list report as password protected XLS zip
     */
    public function downloadReportXls(Request $request, $type)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        list($title, $headers, $rows, $summary) = $this->getReportData($type, $startDate, $endDate);

        $originalTitle = $title;
        if ($startDate && $endDate) {
            $title .= " (" . date('Y-m-d', strtotime($startDate)) . " to " . date('Y-m-d', strtotime($endDate)) . ")";
        } elseif ($startDate) {
            $title .= " (From " . date('Y-m-d', strtotime($startDate)) . ")";
        } elseif ($endDate) {
            $title .= " (Up to " . date('Y-m-d', strtotime($endDate)) . ")";
        }

        // Generate XLS using PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Title
        $sheet->setCellValue('A1', $title);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        
        // Headers
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '3', $header);
            $sheet->getStyle($col . '3')->getFont()->setBold(true);
            $col++;
        }
        
        // Rows
        $rowNum = 4;
        foreach ($rows as $row) {
            $col = 'A';
            foreach ($row as $val) {
                $sheet->setCellValue($col . $rowNum, strip_tags($val));
                $col++;
            }
            $rowNum++;
        }
        
        // Summary section
        $rowNum += 2;
        $sheet->setCellValue('A' . $rowNum, 'SUMMARY');
        $sheet->getStyle('A' . $rowNum)->getFont()->setBold(true);
        $rowNum++;
        
        foreach ($summary as $key => $val) {
            $sheet->setCellValue('A' . $rowNum, $key);
            $sheet->setCellValue('B' . $rowNum, strip_tags($val));
            $sheet->getStyle('A' . $rowNum)->getFont()->setBold(true);
            $rowNum++;
        }

        // Set password for Excel sheet protection
        $password = date('dmY');
        $sheet->getProtection()->setPassword($password);
        $sheet->getProtection()->setSheet(true);

        // Save to temporary XLS file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
        $tempXlsPath = tempnam(sys_get_temp_dir(), 'xls_report');
        $writer->save($tempXlsPath);

        // Create password-protected ZIP archive
        $tempZipPath = tempnam(sys_get_temp_dir(), 'zip_report');
        $zip = new \ZipArchive();
        if ($zip->open($tempZipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            $zip->setPassword($password);
            
            // XLS file name in the zip
            $xlsFileName = strtolower(str_replace(' ', '_', $originalTitle)) . '_' . date('Ymd') . '.xls';
            
            $zip->addFile($tempXlsPath, $xlsFileName);
            if (defined('ZipArchive::EM_AES_256')) {
                $zip->setEncryptionName($xlsFileName, \ZipArchive::EM_AES_256);
            } else {
                $zip->setEncryptionName($xlsFileName, \ZipArchive::EM_TRAD_PKWARE);
            }
            $zip->close();
        }
        
        // Delete temporary XLS file
        @unlink($tempXlsPath);

        return response()->download($tempZipPath, strtolower(str_replace(' ', '_', $originalTitle)) . '_' . date('Ymd') . '.zip', [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Shared helper to retrieve report dataset
     */
    private function getReportData($type, $startDate, $endDate)
    {
        $title = "";
        $headers = [];
        $rows = [];
        $summary = [];

        if ($type === 'users') {
            $title = "Users Registration Report";
            $headers = ['ID', 'Name', 'Email', 'Phone', 'Status', 'Joined'];
            
            $query = User::query();
            $totalCount = User::query();
            $activeCount = User::where('status', 'active');
            $inactiveCount = User::where('status', 'inactive');
            $suspendedCount = User::where('status', 'suspended');
            
            if ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
                $totalCount->whereDate('created_at', '>=', $startDate);
                $activeCount->whereDate('created_at', '>=', $startDate);
                $inactiveCount->whereDate('created_at', '>=', $startDate);
                $suspendedCount->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
                $totalCount->whereDate('created_at', '<=', $endDate);
                $activeCount->whereDate('created_at', '<=', $endDate);
                $inactiveCount->whereDate('created_at', '<=', $endDate);
                $suspendedCount->whereDate('created_at', '<=', $endDate);
            }

            $users = $query->latest()->get();
            foreach ($users as $user) {
                $statusBadge = $user->status === 'active' 
                    ? '<span style="color: green; font-weight: bold;">Active</span>' 
                    : ($user->status === 'inactive' ? '<span style="color: gray;">Inactive</span>' : '<span style="color: red;">Suspended</span>');
                
                $rows[] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? 'N/A',
                    'status' => $statusBadge,
                    'joined' => $user->created_at->format('Y-m-d')
                ];
            }
            $summary = [
                'Total Users' => $totalCount->count(),
                'Active Users' => $activeCount->count(),
                'Inactive Users' => $inactiveCount->count(),
                'Suspended Users' => $suspendedCount->count(),
            ];
        } elseif ($type === 'businesses') {
            $title = "Business Directory Report";
            $headers = ['ID', 'Business Name', 'Owner', 'Category', 'Verification', 'Subscription', 'Start Date', 'End Date'];
            
            $query = Business::with('user');
            $totalCount = Business::query();
            $approvedCount = Business::where('verification_status', 'approved');
            $pendingCount = Business::where('verification_status', 'pending');
            $activeSubCount = Business::where('subscription_status', 'active');
            
            if ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
                $totalCount->whereDate('created_at', '>=', $startDate);
                $approvedCount->whereDate('created_at', '>=', $startDate);
                $pendingCount->whereDate('created_at', '>=', $startDate);
                $activeSubCount->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
                $totalCount->whereDate('created_at', '<=', $endDate);
                $approvedCount->whereDate('created_at', '<=', $endDate);
                $pendingCount->whereDate('created_at', '<=', $endDate);
                $activeSubCount->whereDate('created_at', '<=', $endDate);
            }

            $businesses = $query->latest()->get();
            foreach ($businesses as $b) {
                $rows[] = [
                    'id' => $b->id,
                    'name' => $b->business_name,
                    'owner' => $b->user?->name ?? 'N/A',
                    'category' => $b->category?->name ?? 'N/A',
                    'verification' => ucfirst($b->verification_status),
                    'subscription' => ucfirst($b->subscription_status),
                    'start_date' => $b->created_at ? $b->created_at->format('Y-m-d') : 'N/A',
                    'end_date' => $b->subscription_expires_at ? $b->subscription_expires_at->format('Y-m-d') : 'N/A'
                ];
            }
            $summary = [
                'Total Businesses' => $totalCount->count(),
                'Approved' => $approvedCount->count(),
                'Pending' => $pendingCount->count(),
                'Active Subscriptions' => $activeSubCount->count(),
            ];
        } elseif ($type === 'matrimony') {
            $title = "Matrimonial Profiles Report";
            $headers = ['ID', 'Profile Name', 'Gender', 'Caste', 'Status', 'Registered', 'Start Date', 'End Date'];
            
            $query = MatrimonyProfile::with('user');
            $totalCount = MatrimonyProfile::query();
            $approvedCount = MatrimonyProfile::where('approval_status', 'approved');
            $pendingCount = MatrimonyProfile::where('approval_status', 'pending');
            
            if ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
                $totalCount->whereDate('created_at', '>=', $startDate);
                $approvedCount->whereDate('created_at', '>=', $startDate);
                $pendingCount->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
                $totalCount->whereDate('created_at', '<=', $endDate);
                $approvedCount->whereDate('created_at', '<=', $endDate);
                $pendingCount->whereDate('created_at', '<=', $endDate);
            }

            $profiles = $query->latest()->get();
            foreach ($profiles as $p) {
                $personal = $p->personal_details ?? [];
                $rows[] = [
                    'id' => $p->id,
                    'name' => $p->user?->name ?? 'N/A',
                    'gender' => ucfirst($p->gender ?? 'N/A'),
                    'caste' => $personal['caste'] ?? 'N/A',
                    'status' => ucfirst($p->approval_status),
                    'registered' => $p->created_at->format('Y-m-d'),
                    'start_date' => $p->created_at ? $p->created_at->format('Y-m-d') : 'N/A',
                    'end_date' => $p->profile_expires_at ? $p->profile_expires_at->format('Y-m-d') : 'N/A'
                ];
            }
            $summary = [
                'Total Matrimony Profiles' => $totalCount->count(),
                'Approved' => $approvedCount->count(),
                'Pending' => $pendingCount->count(),
            ];
        } elseif ($type === 'payments') {
            $title = "Payments & Revenue Report";
            $headers = ['Transaction ID', 'User', 'Amount', 'Purpose', 'Status', 'Date', 'Start Date', 'End Date'];
            
            $query = \App\Models\Payment::with(['user.business', 'user.matrimonyProfile']);
            $totalCount = \App\Models\Payment::query();
            $revenueSum = \App\Models\Payment::where('status', 'completed');
            $successCount = \App\Models\Payment::where('status', 'completed');
            $pendingCount = \App\Models\Payment::where('status', 'pending');
            
            if ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
                $totalCount->whereDate('created_at', '>=', $startDate);
                $revenueSum->whereDate('created_at', '>=', $startDate);
                $successCount->whereDate('created_at', '>=', $startDate);
                $pendingCount->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
                $totalCount->whereDate('created_at', '<=', $endDate);
                $revenueSum->whereDate('created_at', '<=', $endDate);
                $successCount->whereDate('created_at', '<=', $endDate);
                $pendingCount->whereDate('created_at', '<=', $endDate);
            }

            $payments = $query->latest()->get();
            foreach ($payments as $pay) {
                $rows[] = [
                    'id' => $pay->transaction_id ?? 'N/A',
                    'user' => $pay->user?->name ?? 'Deleted User',
                    'amount' => 'INR ' . number_format($pay->amount, 2),
                    'purpose' => $pay->purpose ?? 'General',
                    'status' => ucfirst($pay->status),
                    'date' => $pay->created_at->format('Y-m-d'),
                    'start_date' => $pay->subscription_start_date ? \Carbon\Carbon::parse($pay->subscription_start_date)->format('Y-m-d') : 'N/A',
                    'end_date' => $pay->subscription_end_date ? \Carbon\Carbon::parse($pay->subscription_end_date)->format('Y-m-d') : 'N/A'
                ];
            }
            $summary = [
                'Total Transactions' => $totalCount->count(),
                'Completed Revenue' => 'INR ' . number_format($revenueSum->sum('amount'), 2),
                'Successful Payments' => $successCount->count(),
                'Pending Payments' => $pendingCount->count(),
            ];
        }

        return [$title, $headers, $rows, $summary];
    }
}
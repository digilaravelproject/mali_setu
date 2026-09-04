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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordChangedMail;
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
     * Show admin profile settings page
     */
    public function profile()
    {
        $user = Auth::user();
        return view('admin.dashboard.profile', compact('user'));
    }

    /**
     * Update admin profile settings
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone'            => 'required|string|max:15|unique:users,phone,' . $user->id,
            'photo'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'current_password' => 'nullable|required_with:password|string',
            'password'         => 'nullable|string|min:8|confirmed',
        ]);

        $data = $request->only(['name', 'email', 'phone']);

        // Check if user is updating password
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            $data['password'] = Hash::make($request->password);
        }

        // Handle profile photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if (!empty($user->photo)) {
                if (Storage::disk('public')->exists($user->photo)) {
                    Storage::disk('public')->delete($user->photo);
                }
            }

            $file = $request->file('photo');
            $fileName = 'profile/photos/' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            if (!Storage::disk('public')->exists('profile/photos')) {
                Storage::disk('public')->makeDirectory('profile/photos');
            }

            Storage::disk('public')->put($fileName, file_get_contents($file));
            $data['photo'] = $fileName;
        }

        $user->update($data);

        // Send email notification if password was changed
        if ($request->filled('password')) {
            try {
                Mail::to($user->email)->send(new PasswordChangedMail($user, $request->password));
            } catch (\Throwable $mailEx) {
                \Log::warning('Admin password change email failed', [
                    'user_id' => $user->id,
                    'error'   => $mailEx->getMessage()
                ]);
            }
        }

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully!');
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

        $rows = array_map(fn (array $row) => array_map(
            fn ($value) => $this->formatReportValue($value),
            $row
        ), $rows);
        $summary = array_map(fn ($value) => $this->formatReportValue($value), $summary);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'admin.pdf.report_template',
            compact('title', 'headers', 'rows', 'summary')
        )->setPaper('a4', count($headers) > 8 ? 'landscape' : 'portrait');

        return $pdf->download(strtolower(str_replace(' ', '_', $originalTitle)) . '_' . date('Ymd') . '.pdf');
    }

    /**
     * Download list report directly as an unprotected XLS file.
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

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Report');
        
        // Title
        $sheet->setCellValue('A1', $title);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        
        // Headers
        foreach ($headers as $columnIndex => $header) {
            $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex + 1) . '3';
            $sheet->setCellValue($cell, $header);
            $sheet->getStyle($cell)->getFont()->setBold(true);
        }
        
        // Rows
        $rowNum = 4;
        foreach ($rows as $row) {
            foreach (array_values($row) as $columnIndex => $value) {
                $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex + 1) . $rowNum;
                $value = $this->formatReportValue($value);

                if (is_int($value) || is_float($value)) {
                    $sheet->setCellValue($cell, $value);
                } else {
                    // Explicit text keeps phone numbers, pincodes and long IDs out of scientific notation.
                    $sheet->setCellValueExplicit(
                        $cell,
                        strip_tags((string) $value),
                        \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                    );
                }
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
            $sheet->setCellValue('B' . $rowNum, strip_tags((string) $this->formatReportValue($val)));
            $sheet->getStyle('A' . $rowNum)->getFont()->setBold(true);
            $rowNum++;
        }

        $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(max(count($headers), 2));
        $sheet->freezePane('A4');
        $sheet->setAutoFilter("A3:{$lastColumn}3");
        $sheet->getStyle("A3:{$lastColumn}3")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF0D6EFD');
        $sheet->getStyle("A3:{$lastColumn}3")->getFont()->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle("A3:{$lastColumn}{$rowNum}")->getAlignment()->setVertical('top')->setWrapText(true);

        foreach (range(1, max(count($headers), 2)) as $columnIndex) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $fileName = strtolower(str_replace(' ', '_', $originalTitle)) . '_' . date('Ymd') . '.xls';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
            $writer->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, $fileName, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
        ]);
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
            $headers = [
                'ID', 'Name', 'Email', 'Phone', 'Age', 'Date of Birth', 'Occupation',
                'Company', 'Department', 'Designation', 'User Type', 'Account Status',
                'Caste Verification', 'Respected Person', 'Respected Person Phone',
                'Referral Code', 'Address', 'Nearby Location', 'Road Number', 'Sector',
                'Village', 'City', 'District', 'State', 'Country', 'Pincode', 'Latitude',
                'Longitude', 'Email Verified At', 'Blog Access', 'Admin Notes', 'Joined',
                'Last Updated',
            ];
            
            $query = User::query();
            $totalCount = User::query();
            $activeCount = User::where('status', 'active');
            $inactiveCount = User::where('status', 'inactive');
            $suspendedCount = User::where('status', 'suspended');
            $bannedCount = User::where('status', 'banned');
            
            if ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
                $totalCount->whereDate('created_at', '>=', $startDate);
                $activeCount->whereDate('created_at', '>=', $startDate);
                $inactiveCount->whereDate('created_at', '>=', $startDate);
                $suspendedCount->whereDate('created_at', '>=', $startDate);
                $bannedCount->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
                $totalCount->whereDate('created_at', '<=', $endDate);
                $activeCount->whereDate('created_at', '<=', $endDate);
                $inactiveCount->whereDate('created_at', '<=', $endDate);
                $suspendedCount->whereDate('created_at', '<=', $endDate);
                $bannedCount->whereDate('created_at', '<=', $endDate);
            }

            $users = $query->latest()->get();
            foreach ($users as $user) {
                $rows[] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'age' => $user->age,
                    'dob' => $user->dob,
                    'occupation' => $user->occupation,
                    'company' => $user->company_name,
                    'department' => $user->dept_name,
                    'designation' => $user->designation,
                    'user_type' => ucfirst((string) $user->user_type),
                    'status' => ucfirst((string) $user->status),
                    'caste_verification' => ucfirst((string) $user->caste_verification_status),
                    'respected_person' => $user->respected_person_name,
                    'respected_person_phone' => $user->respected_person_mobile_number,
                    'referral_code' => $user->reffral_code,
                    'address' => $user->address,
                    'nearby_location' => $user->nearby_location,
                    'road_number' => $user->road_number,
                    'sector' => $user->sector,
                    'village' => $user->village,
                    'city' => $user->city,
                    'district' => $user->district,
                    'state' => $user->state,
                    'country' => $user->country,
                    'pincode' => $user->pincode,
                    'latitude' => $user->latitude,
                    'longitude' => $user->longitude,
                    'email_verified_at' => $user->email_verified_at,
                    'blog_access' => $user->blog_access,
                    'admin_notes' => $user->admin_notes,
                    'joined' => $user->created_at,
                    'updated' => $user->updated_at,
                ];
            }
            $summary = [
                'Total Users' => $totalCount->count(),
                'Active Users' => $activeCount->count(),
                'Inactive Users' => $inactiveCount->count(),
                'Suspended Users' => $suspendedCount->count(),
                'Banned Users' => $bannedCount->count(),
            ];
        } elseif ($type === 'businesses') {
            $title = "Business Directory Report";
            $headers = [
                'ID', 'Business Name', 'Business Type', 'Owner ID', 'Owner Name', 'Owner Email',
                'Owner Phone', 'Category', 'Description', 'Contact Phone', 'Contact Email',
                'Website', 'Address', 'Village', 'Taluka', 'City', 'District', 'State',
                'Country', 'Pincode', 'Latitude', 'Longitude', 'Opening Time', 'Closing Time',
                'Verification', 'Verified At', 'Verified By', 'Rejection Reason', 'Account Status',
                'Subscription', 'Subscription Expires', 'Job Posting Limit', 'Photos', 'Created',
                'Last Updated',
            ];
            
            $query = Business::with(['user', 'category']);
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
                    'type' => $b->business_type,
                    'owner_id' => $b->user_id,
                    'owner' => $b->user?->name,
                    'owner_email' => $b->user?->email,
                    'owner_phone' => $b->user?->phone,
                    'category' => $b->category?->name,
                    'description' => $b->description,
                    'contact_phone' => $b->contact_phone,
                    'contact_email' => $b->contact_email,
                    'website' => $b->website,
                    'address' => $b->address,
                    'village' => $b->village,
                    'taluka' => $b->taluka,
                    'city' => $b->city,
                    'district' => $b->district,
                    'state' => $b->state,
                    'country' => $b->country,
                    'pincode' => $b->pincode,
                    'latitude' => $b->latitude,
                    'longitude' => $b->longitude,
                    'opening_time' => $b->opening_time,
                    'closing_time' => $b->closing_time,
                    'verification' => ucfirst((string) $b->verification_status),
                    'verified_at' => $b->verified_at,
                    'verified_by' => $b->verified_by,
                    'rejection_reason' => $b->rejection_reason,
                    'status' => ucfirst((string) $b->status),
                    'subscription' => ucfirst((string) $b->subscription_status),
                    'subscription_expires' => $b->subscription_expires_at,
                    'job_posting_limit' => $b->job_posting_limit,
                    'photos' => $b->photo,
                    'created' => $b->created_at,
                    'updated' => $b->updated_at,
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
            $headers = [
                'ID', 'User ID', 'Profile Name', 'Email', 'Phone', 'Gender', 'Date of Birth',
                'Time of Birth', 'Age', 'Height', 'Weight', 'Complexion', 'Physical Status',
                'Personal Details', 'Family Details', 'Education Details', 'Professional Details',
                'Lifestyle Details', 'Location Details', 'Religious Details', 'Partner Preferences',
                'Privacy Settings', 'Approval Status', 'Approved At', 'Approved By',
                'Rejection Reason', 'Account Status', 'Profile Expires', 'Registered', 'Last Updated',
            ];
            
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
                $rows[] = [
                    'id' => $p->id,
                    'user_id' => $p->user_id,
                    'name' => $p->user?->name,
                    'email' => $p->user?->email,
                    'phone' => $p->user?->phone,
                    'gender' => ucfirst((string) $p->gender),
                    'date_of_birth' => $p->date_of_birth,
                    'time_of_birth' => $p->time_of_birth,
                    'age' => $p->age,
                    'height' => $p->height,
                    'weight' => $p->weight,
                    'complexion' => $p->complexion,
                    'physical_status' => $p->physical_status,
                    'personal_details' => $p->personal_details,
                    'family_details' => $p->family_details,
                    'education_details' => $p->education_details,
                    'professional_details' => $p->professional_details,
                    'lifestyle_details' => $p->lifestyle_details,
                    'location_details' => $p->location_details,
                    'religious_details' => $p->religious_details,
                    'partner_preferences' => $p->partner_preferences,
                    'privacy_settings' => $p->privacy_settings,
                    'approval_status' => ucfirst((string) $p->approval_status),
                    'approved_at' => $p->approved_at,
                    'approved_by' => $p->approved_by,
                    'rejection_reason' => $p->rejection_reason,
                    'status' => ucfirst((string) $p->status),
                    'profile_expires' => $p->profile_expires_at,
                    'registered' => $p->created_at,
                    'updated' => $p->updated_at,
                ];
            }
            $summary = [
                'Total Matrimony Profiles' => $totalCount->count(),
                'Approved' => $approvedCount->count(),
                'Pending' => $pendingCount->count(),
            ];
        } elseif ($type === 'payments') {
            $title = "Payments & Revenue Report";
            $headers = [
                'ID', 'Payment ID', 'Order ID', 'Transaction ID', 'User ID', 'User Name',
                'User Email', 'User Phone', 'Payment Type', 'Amount', 'Currency', 'Status',
                'Payment Method', 'Description', 'Paid At', 'Refund Amount', 'Refunded At',
                'Refund Reason', 'Receipt Number', 'Metadata', 'Subscription Start',
                'Subscription End', 'Created', 'Last Updated',
            ];
            
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
                    'id' => $pay->id,
                    'payment_id' => $pay->payment_id,
                    'order_id' => $pay->order_id,
                    'transaction_id' => $pay->transaction_id,
                    'user_id' => $pay->user_id,
                    'user' => $pay->user?->name ?? 'Deleted User',
                    'email' => $pay->user?->email,
                    'phone' => $pay->user?->phone,
                    'payment_type' => $pay->payment_type,
                    'amount' => (float) $pay->amount,
                    'currency' => $pay->currency,
                    'status' => ucfirst((string) $pay->status),
                    'payment_method' => $pay->payment_method,
                    'description' => $pay->description,
                    'paid_at' => $pay->paid_at,
                    'refund_amount' => $pay->refund_amount === null ? null : (float) $pay->refund_amount,
                    'refunded_at' => $pay->refunded_at,
                    'refund_reason' => $pay->refund_reason,
                    'receipt_number' => $pay->receipt_number,
                    'metadata' => $pay->metadata,
                    'subscription_start' => $pay->subscription_start_date,
                    'subscription_end' => $pay->subscription_end_date,
                    'created' => $pay->created_at,
                    'updated' => $pay->updated_at,
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

    /**
     * Convert report values to readable scalar content without exposing credentials.
     */
    private function formatReportValue($value): string|int|float
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_array($value)) {
            $flattened = [];
            array_walk_recursive($value, function ($item, $key) use (&$flattened) {
                $flattened[] = ucwords(str_replace('_', ' ', (string) $key)) . ': ' . $this->formatReportValue($item);
            });

            return $flattened ? implode('; ', $flattened) : 'N/A';
        }

        if ($value === null || $value === '') {
            return 'N/A';
        }

        return is_int($value) || is_float($value) ? $value : (string) $value;
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use App\Models\Business;
use App\Models\Transaction;
use App\Exports\BusinessTransactionsExport;
use App\Exports\MatrimonyTransactionsExport;
use App\Exports\PaymentsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PaymentManagementController extends Controller
{
    /**
     * Display all payments
     */
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'business']);
        
        // Filter by payment status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        // Filter by payment method
        if ($request->has('payment_method') && $request->payment_method !== '') {
            $query->where('payment_method', $request->payment_method);
        }
        
        // Filter by date range
        if ($request->has('date_from') && $request->date_from !== '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to !== '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Search by transaction ID or user
        if ($request->has('search') && $request->search !== '') {
            $query->where(function($q) use ($request) {
                $q->where('transaction_id', 'like', '%' . $request->search . '%')
                  ->orWhere('gateway_payment_id', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('name', 'like', '%' . $request->search . '%')
                               ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
            });
        }
        
        $payments = $query->latest()->paginate(20);
        
        $stats = [
            'total' => Payment::count(),
            'total_amount' => Payment::where('status', 'completed')->sum('amount'),
            'pending' => Payment::where('status', 'pending')->count(),
            'failed' => Payment::where('status', 'failed')->count(),
            'completed' => Payment::where('status', 'completed')->count(),
            'refunded' => Payment::where('status', 'refunded')->count(),
            'today_revenue' => Payment::where('status', 'completed')
                ->whereDate('created_at', today())->sum('amount'),
            'monthly_revenue' => Payment::where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount')
        ];
        
        return view('admin.payments.index', compact('payments', 'stats'));
    }
    
    /**
     * Display payment analytics
     */
    public function analytics(Request $request)
    {
        $period = $request->get('period', '30'); // Default 30 days
        $startDate = Carbon::now()->subDays($period);
        
        // Revenue analytics
        $revenueData = Payment::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        // Payment type breakdown
        $paymentTypes = Payment::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('payment_type, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('payment_type')
            ->get();
            
        // Monthly comparison
        $currentMonth = Payment::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
            
        $lastMonth = Payment::where('status', 'completed')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('amount');
            
        $monthlyGrowth = $lastMonth > 0 ? (($currentMonth - $lastMonth) / $lastMonth) * 100 : 0;
        
        // Top paying users
        $topUsers = Payment::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->with('user')
            ->selectRaw('user_id, SUM(amount) as total_paid, COUNT(*) as payment_count')
            ->groupBy('user_id')
            ->orderByDesc('total_paid')
            ->limit(10)
            ->get();
            
        $stats = [
            'total_revenue' => $revenueData->sum('total'),
            'avg_transaction' => $revenueData->count() > 0 ? $revenueData->sum('total') / Payment::where('status', 'completed')->where('created_at', '>=', $startDate)->count() : 0,
            'monthly_growth' => round($monthlyGrowth, 2),
            'success_rate' => Payment::where('created_at', '>=', $startDate)->count() > 0 ? 
                round((Payment::where('status', 'completed')->where('created_at', '>=', $startDate)->count() / 
                Payment::where('created_at', '>=', $startDate)->count()) * 100, 2) : 0
        ];
        
        return view('admin.payments.analytics', compact(
            'revenueData', 'paymentTypes', 'topUsers', 'stats', 'period'
        ));
    }
    
    /**
     * View payment details
     */
    public function show($id)
    {
        $payment = Payment::with(['user', 'business'])->findOrFail($id);
        
        return view('admin.payments.show', compact('payment'));
    }
    
    /**
     * Process refund
     */
    public function refund(Request $request, $id)
    {
        $request->validate([
            'refund_reason' => 'required|string|max:500',
            'refund_amount' => 'required|numeric|min:1'
        ]);
        
        $payment = Payment::findOrFail($id);
        
        if ($payment->status !== 'completed') {
            return redirect()->back()->with('error', 'Only completed payments can be refunded.');
        }
        
        if ($request->refund_amount > $payment->amount) {
            return redirect()->back()->with('error', 'Refund amount cannot exceed payment amount.');
        }
        
        // Update payment record
        $payment->update([
            'status' => 'refunded',
            'refund_amount' => $request->refund_amount,
            'refund_reason' => $request->refund_reason,
            'refunded_at' => now(),
            'refunded_by' => auth()->id()
        ]);
        
        // Here you would integrate with Razorpay refund API
        // For now, we'll just update the status
        
        return redirect()->back()->with('success', 'Refund processed successfully!');
    }
    
    /**
     * Export payments data
     */
    public function export(Request $request)
    {
        $query = Payment::with(['user', 'business']);
        
        // Apply same filters as index
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('payment_type') && $request->payment_type !== '') {
            $query->where('payment_type', $request->payment_type);
        }
        
        if ($request->has('date_from') && $request->date_from !== '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to !== '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $payments = $query->get();
        
        $filename = 'payments_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $filename = $filename;
        $callback = function() use ($payments) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Payment ID', 'User Name', 'User Email', 'Amount', 'Payment Type',
                'Status', 'Razorpay Payment ID', 'Razorpay Order ID', 'Created At'
            ]);

            // CSV data
            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->id,
                    $payment->user->name ?? 'N/A',
                    $payment->user->email ?? 'N/A',
                    $payment->amount,
                    $payment->payment_type,
                    $payment->status,
                    $payment->razorpay_payment_id,
                    $payment->razorpay_order_id,
                    $payment->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * List business subscription transactions
     */
    public function businessTransactions(Request $request)
    {
        $query = Transaction::with('user')->where('purpose', 'business_registration');

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from') && $request->date_from !== '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to !== '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->has('search') && $request->search !== '') {
            $query->where(function($q) use ($request) {
                $q->where('razorpay_order_id', 'like', '%' . $request->search . '%')
                  ->orWhere('razorpay_payment_id', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('name', 'like', '%' . $request->search . '%')
                               ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $transactions = $query->latest()->paginate(20);

        return view('admin.payments.business_transactions', compact('transactions'));
    }

    /**
     * Export business subscription transactions CSV
     */
    public function exportBusiness(Request $request)
    {
        $query = Transaction::with('user')->where('purpose', 'business_registration');

        if ($request->has('date_from') && $request->date_from !== '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to !== '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->get();

        $filename = 'business_transactions_' . date('Y-m-d_H-i-s') . '.csv';

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Transaction ID','User Name','User Email','Amount','Purpose','Status','Razorpay Payment ID','Razorpay Order ID','Created At']);
            foreach ($transactions as $t) {
                fputcsv($file, [
                    $t->id,
                    $t->user->name ?? 'N/A',
                    $t->user->email ?? 'N/A',
                    $t->amount,
                    $t->purpose,
                    $t->status,
                    $t->razorpay_payment_id ?? '',
                    $t->razorpay_order_id ?? '',
                    $t->created_at->format('Y-m-d H:i:s')
                ]);
            }
            fclose($file);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * List matrimony subscription transactions
     */
    public function matrimonyTransactions(Request $request)
    {
        $query = Transaction::with('user')->where('purpose', 'matrimony_profile');

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from') && $request->date_from !== '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to !== '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->has('search') && $request->search !== '') {
            $query->where(function($q) use ($request) {
                $q->where('razorpay_order_id', 'like', '%' . $request->search . '%')
                  ->orWhere('razorpay_payment_id', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('name', 'like', '%' . $request->search . '%')
                               ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $transactions = $query->latest()->paginate(20);

        return view('admin.payments.matrimony_transactions', compact('transactions'));
    }

    /**
     * Export matrimony subscription transactions CSV
     */
    public function exportMatrimony(Request $request)
    {
        $query = Transaction::with('user')->where('purpose', 'matrimony_profile');

        if ($request->has('date_from') && $request->date_from !== '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to !== '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->get();

        $filename = 'matrimony_transactions_' . date('Y-m-d_H-i-s') . '.csv';

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Transaction ID','User Name','User Email','Amount','Purpose','Status','Razorpay Payment ID','Razorpay Order ID','Created At']);
            foreach ($transactions as $t) {
                fputcsv($file, [
                    $t->id,
                    $t->user->name ?? 'N/A',
                    $t->user->email ?? 'N/A',
                    $t->amount,
                    $t->purpose,
                    $t->status,
                    $t->razorpay_payment_id ?? '',
                    $t->razorpay_order_id ?? '',
                    $t->created_at->format('Y-m-d H:i:s')
                ]);
            }
            fclose($file);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * View transaction detail
     */
    public function showTransaction($id)
    {
        $transaction = Transaction::with('user')->findOrFail($id);
        return view('admin.payments.transaction_show', compact('transaction'));
    }
    
    /**
     * Update payment status manually
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,completed,failed,refunded',
            'admin_notes' => 'nullable|string|max:500'
        ]);
        
        $payment = Payment::findOrFail($id);
        
        $payment->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'updated_by' => auth()->id()
        ]);
        
        return redirect()->back()->with('success', 'Payment status updated successfully!');
    }

    /**
     * Export business transactions to Excel
     */
    public function exportBusinessXlsx(Request $request)
    {
        $query = Transaction::with('user')->where('purpose', 'business_registration');

        if ($request->has('date_from') && $request->date_from !== '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to !== '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->get();
        $filename = 'business_transactions_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new BusinessTransactionsExport($transactions), $filename);
    }

    /**
     * Export matrimony transactions to Excel
     */
    public function exportMatrimonyXlsx(Request $request)
    {
        $query = Transaction::with('user')->where('purpose', 'matrimony_profile');

        if ($request->has('date_from') && $request->date_from !== '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to !== '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->get();
        $filename = 'matrimony_transactions_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new MatrimonyTransactionsExport($transactions), $filename);
    }

    /**
     * Export all payments to Excel
     */
    public function exportXlsx(Request $request)
    {
        $query = Payment::with(['user', 'business']);
        
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from') && $request->date_from !== '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to !== '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->get();
        $filename = 'payments_export_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new PaymentsExport($payments), $filename);
    }
}
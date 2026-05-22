<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Payment;
use App\Models\Business;
use App\Models\MatrimonyProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the subscriptions/transactions.
     */
    public function index(Request $request)
    {
        $query = Transaction::where('user_id', Auth::id());

        // Apply Status Filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Apply Purpose Filter
        if ($request->filled('purpose') && $request->purpose !== 'all') {
            $query->where('purpose', $request->purpose);
        }

        // Apply Date Filter
        if ($request->filled('date_range') && $request->date_range !== 'all') {
            switch ($request->date_range) {
                case '30_days':
                    $query->where('created_at', '>=', Carbon::now()->subDays(30));
                    break;
                case '6_months':
                    $query->where('created_at', '>=', Carbon::now()->subMonths(6));
                    break;
                case '12_months':
                    $query->where('created_at', '>=', Carbon::now()->subMonths(12));
                    break;
            }
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('subscription.index', compact('transactions'));
    }

    /**
     * Display the specified subscription/transaction.
     */
    public function show($id)
    {
        $transaction = Transaction::where('user_id', Auth::id())->findOrFail($id);
        $payment = Payment::where('transaction_id', $transaction->id)->first();

        // Calculate subscription validity if applicable
        $startDate = null;
        $endDate = null;
        $profileOrBusiness = null;
        $statusActive = false;

        if ($transaction->status === 'completed') {
            $startDate = $payment ? $payment->paid_at : $transaction->updated_at;
            if ($transaction->subscription_period) {
                $endDate = Carbon::parse($startDate)->addMonths($transaction->subscription_period);
            }

            // Fetch details of active status
            if ($transaction->purpose === 'business_registration') {
                $profileOrBusiness = Business::where('user_id', Auth::id())->latest()->first();
                if ($profileOrBusiness && $profileOrBusiness->subscription_status === 'active' && (!$profileOrBusiness->subscription_expires_at || Carbon::parse($profileOrBusiness->subscription_expires_at)->isFuture())) {
                    $statusActive = true;
                }
            } elseif ($transaction->purpose === 'matrimony_profile') {
                $profileOrBusiness = MatrimonyProfile::where('user_id', Auth::id())->first();
                if ($profileOrBusiness && (!$profileOrBusiness->profile_expires_at || Carbon::parse($profileOrBusiness->profile_expires_at)->isFuture())) {
                    $statusActive = true;
                }
            }
        }

        return view('subscription.show', compact('transaction', 'payment', 'startDate', 'endDate', 'profileOrBusiness', 'statusActive'));
    }

    /**
     * Show a print-friendly invoice for a completed transaction.
     */
    public function invoice($id)
    {
        $transaction = Transaction::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->findOrFail($id);
            
        $payment = Payment::where('transaction_id', $transaction->id)->first();
        
        $startDate = $payment ? $payment->paid_at : $transaction->updated_at;
        $endDate = $transaction->subscription_period 
            ? Carbon::parse($startDate)->addMonths($transaction->subscription_period) 
            : null;

        return view('subscription.invoice', compact('transaction', 'payment', 'startDate', 'endDate'));
    }
}

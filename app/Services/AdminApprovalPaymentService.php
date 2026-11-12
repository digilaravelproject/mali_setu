<?php

namespace App\Services;

use App\Models\Business;
use App\Models\BusinessPlan;
use App\Models\MatrimonyPlan;
use App\Models\MatrimonyProfile;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AdminApprovalPaymentService
{
    public function approveMatrimony(MatrimonyProfile $profile, MatrimonyPlan $plan, int $adminId): Payment
    {
        return DB::transaction(function () use ($profile, $plan, $adminId) {
            $profile = MatrimonyProfile::query()->lockForUpdate()->findOrFail($profile->id);
            $this->ensureNoPayment($profile->user_id, 'matrimony_subscription');
            $this->ensureActivePlan($plan);

            $approvedAt = now();
            $profile->update([
                'approval_status' => 'approved',
                'approved_at' => $approvedAt,
                'approved_by' => $adminId,
                'profile_expires_at' => $approvedAt->copy()->addYears((int) $plan->duration_years),
            ]);

            return $this->createPayment(
                $profile->user_id,
                'matrimony_subscription',
                $plan->price,
                $plan->id,
                $plan->plan_name ?: 'Matrimony plan',
                $plan->duration_years,
                $adminId
            );
        });
    }

    public function approveBusiness(Business $business, BusinessPlan $plan, int $adminId): Payment
    {
        return DB::transaction(function () use ($business, $plan, $adminId) {
            $business = Business::query()->lockForUpdate()->findOrFail($business->id);
            $this->ensureNoPayment($business->user_id, 'business_registration');
            $this->ensureActivePlan($plan);

            $approvedAt = now();
            $business->update([
                'verification_status' => 'approved',
                'verified_at' => $approvedAt,
                'verified_by' => $adminId,
                'subscription_status' => 'active',
                'subscription_expires_at' => $approvedAt->copy()->addYears((int) $plan->duration_years),
            ]);

            return $this->createPayment(
                $business->user_id,
                'business_registration',
                $plan->price,
                $plan->id,
                $plan->company_type,
                $plan->duration_years,
                $adminId
            );
        });
    }

    private function ensureNoPayment(int $userId, string $paymentType): void
    {
        if (Payment::where('user_id', $userId)->where('payment_type', $paymentType)->exists()) {
            throw ValidationException::withMessages([
                'payment' => 'A payment entry already exists for this user.',
            ]);
        }
    }

    private function ensureActivePlan(MatrimonyPlan|BusinessPlan $plan): void
    {
        if (! $plan->active || (int) $plan->duration_years < 1) {
            throw ValidationException::withMessages([
                'plan_id' => 'The selected subscription plan is not active.',
            ]);
        }
    }

    private function createPayment(
        int $userId,
        string $paymentType,
        mixed $amount,
        int $planId,
        string $planName,
        int $durationYears,
        int $adminId
    ): Payment {
        $reference = 'ADMIN-'.now()->format('YmdHis').'-'.Str::upper(Str::random(8));

        return Payment::create([
            'user_id' => $userId,
            'payment_id' => $reference,
            'order_id' => $reference,
            'payment_type' => $paymentType,
            'amount' => $amount,
            'currency' => 'INR',
            'status' => 'completed',
            'payment_method' => 'other',
            'metadata' => [
                'approved_by_admin' => true,
                'approved_by' => $adminId,
                'plan_id' => $planId,
                'plan_name' => $planName,
                'duration_years' => $durationYears,
            ],
            'description' => $planName.' - approved with payment by admin',
            'paid_at' => now(),
            'receipt_number' => $reference,
        ]);
    }
}

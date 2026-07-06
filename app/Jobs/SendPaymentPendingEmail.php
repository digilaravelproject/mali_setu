<?php

namespace App\Jobs;

use App\Models\Business;
use App\Models\MatrimonyProfile;
use App\Services\NotificationService;
use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendPaymentPendingEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $type;
    protected $id;

    /**
     * Create a new job instance.
     */
    public function __construct(string $type, int $id)
    {
        $this->type = $type;
        $this->id = $id;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Log::info("Running SendPaymentPendingEmail job for {$this->type} #{$this->id}");

        if ($this->type === 'business') {
            $business = Business::find($this->id);
            if (!$business) {
                return;
            }
            
            // Check if business has no active subscription (e.g. subscription_expires_at is null or in the past)
            $isPaid = $business->subscription_status === 'active' && 
                      $business->subscription_expires_at && 
                      \Carbon\Carbon::parse($business->subscription_expires_at)->isFuture();

            if (!$isPaid) {
                try {
                    app(NotificationService::class)->createNotification(
                        $business->user_id,
                        'payment_pending_reminder',
                        'Business Listing Subscription Payment Pending',
                        'You created your business listing "' . $business->business_name . '", but the subscription payment is pending. Please complete your payment to activate and verify your business listing.',
                        ['business_id' => $business->id],
                        '/dashboard/business/subscription',
                        Notification::PRIORITY_HIGH,
                        $business,
                        ['in_app', 'email']
                    );
                    Log::info("Payment pending email sent for business #{$this->id}");
                } catch (\Exception $e) {
                    Log::error("Failed to send payment pending email for business #{$this->id}: " . $e->getMessage());
                }
            }
        } elseif ($this->type === 'matrimony') {
            $profile = MatrimonyProfile::find($this->id);
            if (!$profile) {
                return;
            }

            // Check if profile has no active subscription (e.g. profile_expires_at is null or in the past)
            $isPaid = $profile->profile_expires_at && 
                      \Carbon\Carbon::parse($profile->profile_expires_at)->isFuture();

            if (!$isPaid) {
                try {
                    app(NotificationService::class)->createNotification(
                        $profile->user_id,
                        'payment_pending_reminder',
                        'Matrimony Profile Subscription Payment Pending',
                        'You created your matrimony profile, but the subscription payment is pending. Please complete your payment to activate and verify your matrimony profile.',
                        ['profile_id' => $profile->id],
                        '/dashboard/matrimony/subscription',
                        Notification::PRIORITY_HIGH,
                        $profile,
                        ['in_app', 'email']
                    );
                    Log::info("Payment pending email sent for matrimony profile #{$this->id}");
                } catch (\Exception $e) {
                    Log::error("Failed to send payment pending email for matrimony profile #{$this->id}: " . $e->getMessage());
                }
            }
        }
    }
}

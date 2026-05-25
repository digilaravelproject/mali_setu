<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\BusinessPlan;

$plans = BusinessPlan::where('active', true)->get();

foreach (User::whereHas('business')->get() as $user) {
    $user->load('business');
    $displayPlans = collect($plans);
    if ($user->business && $user->business->business_type) {
        $businessType = trim(str_replace(' ', '', $user->business->business_type));
        $displayPlans = $displayPlans->filter(function($p) use ($businessType) {
            $planType = trim(str_replace(' ', '', $p->company_type ?? ''));
            return $planType === $businessType;
        });
    }
    echo "User ID: {$user->id} | Business Type: '{$user->business->business_type}' | Filtered Plans count: " . $displayPlans->count() . "\n";
    foreach ($displayPlans as $p) {
        echo "  - Plan ID: {$p->id} | Type: '{$p->company_type}' | Price: {$p->price}\n";
    }
}

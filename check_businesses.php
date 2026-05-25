<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Business;

$businesses = Business::all();
foreach ($businesses as $b) {
    echo "ID: {$b->id} | User ID: {$b->user_id} | Name: {$b->business_name} | Type: '{$b->business_type}' | Sub Status: {$b->subscription_status} | Sub Expires: {$b->subscription_expires_at}\n";
}

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    // config/services.php
    'razorpay' => [
        'key'    => env('RAZORPAY_KEY_ID'),
        'secret' => env('RAZORPAY_KEY_SECRET'),
    ],

    'ccavenue' => [
        'merchant_id' => env('CCAVENUE_MERCHANT_ID'),
        'working_key' => env('CCAVENUE_WORKING_KEY'),
        'access_code' => env('CCAVENUE_ACCESS_CODE'),
        // CCAvenue Status API credentials are issued for a registered server IP
        // and can differ from the hosted-checkout credentials above.
        'api_working_key' => env('CCAVENUE_API_WORKING_KEY'),
        'api_access_code' => env('CCAVENUE_API_ACCESS_CODE'),
        'sandbox'     => env('CCAVENUE_SANDBOX', true),
        'status_url'  => env('CCAVENUE_STATUS_URL', 'https://api.ccavenue.com/apis/servlet/DoWebTrans'),
        'status_timeout' => env('CCAVENUE_STATUS_TIMEOUT', 15),
        'reconcile_lookback_days' => env('CCAVENUE_RECONCILE_LOOKBACK_DAYS', 7),
        'reconcile_batch_size' => env('CCAVENUE_RECONCILE_BATCH_SIZE', 100),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT_URL', '/auth/google/callback'),
    ],

    'facebook' => [
        'client_id'     => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect'      => env('FACEBOOK_REDIRECT_URL'),
    ],

];

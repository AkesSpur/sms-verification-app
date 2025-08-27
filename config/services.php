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

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'smsactivate' => [
        'api_key' => env('SMS_ACTIVATE_API_KEY'),
        'base_url' => env('SMS_ACTIVATE_BASE_URL', 'https://api.sms-activate.org/stubs/handler_api.php'),
        'timeout' => env('SMS_ACTIVATE_TIMEOUT', 30),
        'max_retries' => env('SMS_ACTIVATE_MAX_RETRIES', 3),
        'retry_delay' => env('SMS_ACTIVATE_RETRY_DELAY', 1000), // milliseconds
    ],

    'smspool' => [
        'api_key' => env('SMSPOOL_API_KEY'),
        'base_url' => env('SMSPOOL_BASE_URL', 'https://api.smspool.net'),
        'timeout' => env('SMSPOOL_TIMEOUT', 30),
        'max_retries' => env('SMSPOOL_MAX_RETRIES', 3),
        'retry_delay' => env('SMSPOOL_RETRY_DELAY', 1000), // milliseconds
    ],

    'owlet' => [
        'api_key' => env('OWLET_API_KEY'),
        'base_url' => env('OWLET_BASE_URL', 'https://the-owlet.com/api/v2'),
        'timeout' => env('OWLET_TIMEOUT', 30),
    ],

    'exchange_rate' => [
        'api_key' => env('EXCHANGE_RATE_API_KEY'),
        'base_url' => env('EXCHANGE_RATE_BASE_URL', 'https://api.exchangerate-api.com/v4/latest'),
        'timeout' => env('EXCHANGE_RATE_TIMEOUT', 30),
        'markup_percentage' => env('EXCHANGE_RATE_MARKUP', 0),
        'log_requests' => env('EXCHANGE_RATE_LOG_REQUESTS', false),
    ],

    'daisysms' => [
        'api_key' => env('DAISYSMS_API_KEY'),
        'base_url' => env('DAISYSMS_BASE_URL', 'https://daisysms.com/stubs/handler_api.php'),
        'timeout' => env('DAISYSMS_TIMEOUT', 30),
        'log_requests' => env('DAISYSMS_LOG_REQUESTS', true),
    ],
];

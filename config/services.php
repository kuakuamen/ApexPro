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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'gemini' => [
        'api_key'   => env('GEMINI_API_KEY'),
        'api_key_2' => env('GEMINI_API_KEY_2'),
        'api_key_3' => env('GEMINI_API_KEY_3'),
    ],

    'youtube' => [
        'api_key' => env('YOUTUBE_API_KEY'),
    ],

    'mercadopago' => [
        'access_token' => env('MP_ACCESS_TOKEN'),
        'public_key' => env('MP_PUBLIC_KEY'),
        'test_access_token' => env('MP_TEST_ACCESS_TOKEN'),
        'test_public_key' => env('MP_TEST_PUBLIC_KEY'),
        'mode' => env('MP_MODE', 'live'),
        'webhook_secret' => env('MP_WEBHOOK_SECRET'),
        'back_url' => env('MP_BACK_URL'),
        'processing_window_hours' => env('MP_PROCESSING_WINDOW_HOURS', 3),
        'signup_trial_days' => env('MP_SIGNUP_TRIAL_DAYS', 0),
    ],

    'asaas' => [
        'api_key' => env('ASAAS_API_KEY'),
        'env' => env('ASAAS_ENV', 'sandbox'),
        'base_url' => env('ASAAS_ENV', 'sandbox') === 'production'
            ? 'https://api.asaas.com/v3'
            : 'https://api-sandbox.asaas.com/v3',
        'checkout_base_url' => env(
            'ASAAS_CHECKOUT_BASE_URL',
            env('ASAAS_ENV', 'sandbox') === 'production'
                ? 'https://asaas.com/checkoutSession/show?id='
                : 'https://sandbox.asaas.com/checkoutSession/show?id='
        ),
        'webhook_token' => env('ASAAS_WEBHOOK_TOKEN'),
        'trial_days' => max(7, (int) env('ASAAS_TRIAL_DAYS', 7)),
        'checkout_expire_minutes' => (int) env('ASAAS_CHECKOUT_EXPIRE_MINUTES', 60),
        'processing_window_hours' => (int) env('ASAAS_PROCESSING_WINDOW_HOURS', 3),
        'block_cutoff_hour' => (int) env('ASAAS_BLOCK_CUTOFF_HOUR', 6),
    ],

];

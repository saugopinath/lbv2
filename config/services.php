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

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'elk' => [
        'host' => env('ELK_HOST'),
        'host_ip' => env('ELK_HOST_IP'),
        'username' => env('ELK_USERNAME'),
        'password' => env('ELK_PASSWORD')
    ],
    'adv' => [
        'url' => env('ADV_VAULT_URL'),
        'key' => env('ADV_VAULT_KEY'),
    ],
    'doc_storage' => [
        'base_url' => env('DOC_STORAGE_BASE_URL'),
        'app_id' => env('DOC_STORAGE_APP_ID'),
        'client_secret' => env('DOC_STORAGE_CLIENT_SECRET'),
        'type' => env('DOC_STORAGE_TYPE'),
    ],

];

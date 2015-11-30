<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'mandrill' => [
        'secret' => env('MANDRILL_SECRET'),
    ],

    'ses' => [
        'key'    => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'stripe' => [
        'model'  => App\Models\User::class,
        'key'    => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    // Options for Game Army Module.
    'gamearmy' => [
        'baseArmyLevel' => 1,
        'baseArmySize' => 100,
    ],

    // Options for Gamefield Module.
    'gamefield' => [
        'model' => App\Models\Castle::class,  // A model in which are stored the location.
        'height' => env('GAMEFIELD_HEIGHT'),
        'width' => env('GAMEFIELD_WIDTH'),
        'speed' => env('GAMEFIELD_SPEED'),
        'bounds' => env('CASTLE_BOUNDS')
    ]
];

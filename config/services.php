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
        'baseArmyLevel' => env('GAMEARMY_BASE_LEVEL'), // Базовый уровень армии по умолчанид.
        'baseArmySize' => env('GAMEARMY_BASE_SIZE'), // Базовый размер армии по умолчаниж.
        'fatigueArmy' => env('GAMEARMY_FATIGUE'), // Усталость армии. Влияет на скорость возвращения домой.
        // Количество едениц ресурсов, которое может унести один воин в случаи разграбления вражеского замка...
        'resourcesPerRob' => [
            'gold' => env('GAMEARMY_GOLD_PER_ROB'), // золота на воина
            'food' => env('GAMEARMY_FOOD_PER_ROB'), // еды на воина
            'iron' => env('GAMEARMY_IRON_PER_ROB')  // железа на воина
        ]
    ],

    // Options for Gamefield Module.
    'gamefield' => [
        'model' => App\Models\Castle::class,  // Модель в которой хранится координаты положения на игровом поле.
        'height' => env('GAMEFIELD_HEIGHT'), // Размеры игрового поля - высота.
        'width' => env('GAMEFIELD_WIDTH'),  // Размеры игрового поля - ширина.
        'speed' => env('GAMEFIELD_SPEED'),  // Скорость перемешния на игровом поле.
        'bounds' => env('GAMEFIELD_CASTLE_BOUND') // Границы замка (периметр) вокруг которого не могут располагаться другие замки.
    ]
];

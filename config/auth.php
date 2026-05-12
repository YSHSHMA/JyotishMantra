<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | here which uses session storage and the Eloquent user provider.
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | Supported: "session", "token"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'passport',
            'provider' => 'users',
        ],

        'influencer' => [
            'driver' => 'session',
            'provider' => 'astrologers',
        ],

        'influencer_api' => [
            'driver' => 'passport',
            'provider' => 'astrologers',
        ],

        'admin' => [
            'driver' => 'session',
            'provider' => 'admins',
        ],

        'seller' => [
            'driver' => 'session',
            'provider' => 'sellers',
        ],

        'seller-api' => [
            'driver' => 'passport',
            'provider' => 'sellers',
        ],
        'employee' => [
            'driver' => 'passport',
            'provider' => 'tours_employee',
        ],

        'tour' => [
            'driver' => 'session',
            'provider' => 'tours',
        ],

        'event' => [
            'driver' => 'session',
            'provider' => 'events',
        ],
        'trust' => [
            'driver' => 'session',
            'provider' => 'trusts',
        ],

        'customer' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'tour_employee' => [
            'driver' => 'session',
            'provider' => 'tours_employee',
        ],

        'event_employee' => [
            'driver' => 'session',
            'provider' => 'events_employee',
        ],
        'trust_employee' => [
            'driver' => 'session',
            'provider' => 'trusts_employee',
        ],
        'purohit' => [
            'driver' => 'session',
            'provider' => 'purohits',
        ],
        'guruji' => [
            'driver' => 'session',
            'provider' => 'guruji',
        ],
        'collector' => [
            'driver' => 'session',
            'provider' => 'collectors',
        ],
        'collector_api' => [
            'driver' => 'passport',
            'provider' => 'collectors',
        ],
        // 'sdm' => [
        //     'driver' => 'session',
        //     'provider' => 'sdms',
        // ],
        // 'sdm_api' => [
        //     'driver' => 'passport',
        //     'provider' => 'sdms',
        // ],
        // 'sdm_employee' => [
        //     'driver' => 'session',
        //     'provider' => 'sdm_employees',
        // ],
        // 'sdm_employee_api' => [
        //     'driver' => 'passport',
        //     'provider' => 'sdms',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | If you have multiple user tables or models you may configure multiple
    | sources which represent each model / table. These sources may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\User::class,
        ],

        'admins' => [
            'driver' => 'eloquent',
            'model' => \App\Models\Admin::class,
        ],

        'sellers' => [
            'driver' => 'eloquent',
            'model' => \App\Models\Seller::class,
        ],

        "tours" => [
            'driver' => 'eloquent',
            'model' => \App\Models\Seller::class,
        ],

        "events" => [
            'driver' => 'eloquent',
            'model' => \App\Models\Seller::class,
        ],
        "trusts" => [
            'driver' => 'eloquent',
            'model' => \App\Models\Seller::class,
        ],

        'astrologers' => [
            'driver' => 'eloquent',
            'model' => \App\Models\Astrologer\Astrologer::class,
        ],
        "tours_employee" => [
            'driver' => 'eloquent',
            'model' => \App\Models\VendorEmployees::class,
        ],

        "events_employee" => [
            'driver' => 'eloquent',
            'model' => \App\Models\VendorEmployees::class,
        ],
        "trusts_employee" => [
            'driver' => 'eloquent',
            'model' => \App\Models\VendorEmployees::class,
        ],
        "purohits" => [
            'driver' => 'eloquent',
            'model' => \App\Models\Purohit::class,
        ],
        "guruji" => [
            'driver' => 'eloquent',
            'model' => \App\Models\Astrologer\Astrologer::class,
        ],
        "collectors" => [
            'driver' => 'eloquent',
            'model' => \App\Models\Collector::class,
        ],
        // "sdms" => [
        //     'driver' => 'eloquent',
        //     'model' => \App\Models\SDM::class,
        // ],
        // "sdm_employees" => [
        //     'driver' => 'eloquent',
        //     'model' => \App\Models\SDMEmployee::class,
        // ],
        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | You may specify multiple password reset configurations if you have more
    | than one user table or model in the application and you want to have
    | separate password reset settings based on the specific user types.
    |
    | The expire time is the number of minutes that the reset token should be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],

        'admins' => [
            'provider' => 'admins',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],

        'sellers' => [
            'provider' => 'sellers',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
        'tours' => [
            'provider' => 'tours',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
        'events' => [
            'provider' => 'events',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
        'trusts' => [
            'provider' => 'trusts',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
        'astrologers' => [
            'provider' => 'astrologers',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
        'tours_employee' => [
            'provider' => 'tours_employee',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
        'events_employee' => [
            'provider' => 'events_employee',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
        'trusts_employee' => [
            'provider' => 'trusts_employee',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
        'purohits' => [
            'provider' => 'purohits',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
        'collectors' => [
            'provider' => 'collectors',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
        // 'sdms' => [
        //     'provider' => 'sdms',
        //     'table' => 'password_resets',
        //     'expire' => 60,
        //     'throttle' => 60,
        // ],
        // 'sdm_employees' => [
        //     'provider' => 'sdm_employees',
        //     'table' => 'password_resets',
        //     'expire' => 60,
        //     'throttle' => 60,
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Here you may define the amount of seconds before a password confirmation
    | times out and the user is prompted to re-enter their password via the
    | confirmation screen. By default, the timeout lasts for three hours.
    |
    */

    'password_timeout' => 10800,

];

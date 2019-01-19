<?php

return [
    'database'                => [
        'host'     => 'mysql',
        'database' => 'engelsystem',
        'username' => 'angel',
        'password' => 'heaven',
    ],

    'environment'             => env('ENVIRONMENT', 'development'),

    'footer_items'            => [],

    // Email config
    'email'                   => [
        // Can be mail, smtp, sendmail or log
        'driver' => 'log',
        'from'   => [
            // From address of all emails
            'address' => 'noreply@localhost',
            'name'    => 'Engelsystem Dev',
        ],
    ],
];

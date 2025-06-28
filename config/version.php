<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Version
    |--------------------------------------------------------------------------
    |
    | This value is the version of your application. This value is used when
    | the framework needs to place the application's version in a notification
    | or any other location as required by the application or its packages.
    */

    'version' => env('APP_VERSION', '1.0.0'),

    /*
    |--------------------------------------------------------------------------
    | Build Number
    |--------------------------------------------------------------------------
    |
    | This value represents the build number of your application. This can be
    | used for CI/CD processes to track deployments.
    */

    'build' => env('APP_BUILD', 'dev'),

    /*
    |--------------------------------------------------------------------------
    | Release Date
    |--------------------------------------------------------------------------
    |
    | The date when this version was released.
    */

    'release_date' => env('APP_RELEASE_DATE', now()->toDateString()),
];
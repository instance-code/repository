<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Repositories Name
    |--------------------------------------------------------------------------
    | Default repository directory
    |
    */
    'path' => 'app/Repositories',

    /*
     * Default repository namespace
     */
    'namespace' => 'App\Repositories',

    'naming' => 'singular', // plural | singular

    'response' => [
        'headers' => [
           'Content-Type' => 'application/json,charset=UTF-8',
            // 'Access-Control-Allow-Origin' => 'http://localhost.com(API呼び出し元ドメイン)',
            'Access-Control-Allow-Credentials' => 'TRUE',
            'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, DELETE, PUT, PATCH',
            'Access-Control-Allow-Headers' => 'x-requested-with',
            'Access-Control-Max-Age' => '864,000',
        ],
    ],

    /*
     * Default model generate
     */
    'model' => [
        'namespace' => 'App\Models',
        'path' => 'app/Models',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default handler
    |--------------------------------------------------------------------------
    | set false if want custom handler
    | ValidationException
    | UnauthorizedHttpException
    | FatalErrorException
    | QueryException
    */
    'default_handler' => true,

    /*
     * Default binding
     * [ RepoInterface::class => Repository::class ]
     */
    'bindings' => [

    ],

];

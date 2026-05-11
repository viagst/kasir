<?php

return [

    'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],

    'api' => [
        'driver' => 'token',
        'provider' => 'users',
        'hash' => false,
    ],

    'admin' => [ // TAMBAH INI
        'driver' => 'sanctum',
        'provider' => 'admins',
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],

    'admins' => [ // TAMBAH INI
        'driver' => 'eloquent',
        'model' => App\Models\Admin::class,
    ],
],

];
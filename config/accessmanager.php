<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Version
    |--------------------------------------------------------------------------
    |
    | Versión de paquete.
    |
    */
    'version' => env('ACCESS_MANAGER_VERSION', 'v1'),

    /*
    |--------------------------------------------------------------------------
    | Default data
    |--------------------------------------------------------------------------
    |
    | Datos por defecto para los endpoint's del paquete.
    |
    */
    'default_page' => env('ACCESS_MANAGER_DEFAULT_PAGE', 1),
    'default_size' => env('ACCESS_MANAGER_DEFAULT_SIZE', 20),
    'max_size' => env('ACCESS_MANAGER_MAX_SIZE', 100),
    'default_skip' => env('ACCESS_MANAGER_DEFAULT_SKIP', 0),
    'default_take' => env('ACCESS_MANAGER_DEFAULT_TAKE', 20),
    'max_take' => env('ACCESS_MANAGER_MAX_TAKE', 100),

    /*
    |--------------------------------------------------------------------------
    | User entity
    |--------------------------------------------------------------------------
    |
    | Modelo de Usuario para la gestión de autenticación.
    |
    */
    'user_entity' => [
        'model' => App\Models\User::class,
        'table' => 'users'
    ],

    /*
    |--------------------------------------------------------------------------
    | Sync
    |--------------------------------------------------------------------------
    |
    | Sincronización de en cascada de módulos, permisos y rutas para la gestión de roles.
    |
    */
    'strict_sync' => env('ACCESS_MANAGER_STRICT_SYNC', false),
];

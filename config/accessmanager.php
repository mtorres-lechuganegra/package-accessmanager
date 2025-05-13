<?php

return [
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
    | Access
    |--------------------------------------------------------------------------
    |
    | Cascada de módulos, permisos y rutas para la gestión de roles.
    |
    */
    'seeders' => [
        'modules' => [
            // Capability roles
            'capability_roles' => [
                'name' => 'Capacidad de roles',
                'permissions' => [
                    'capability_role_create' => [
                        'name' => 'Crear capacidad de rol',
                        'type' => 'action',
                        'route' => 'api.access.capability.roles.create', // Ruta asociada al permiso
                    ],
                    'capability_role_update' => [
                        'name' => 'Actualizar capacidad de rol',
                        'type' => 'action',
                        'route' => 'api.access.capability.roles.update', // Ruta asociada al permiso
                    ],
                    'capability_role_delete' => [
                        'name' => 'Eliminar capacidad de rol',
                        'type' => 'action',
                        'route' => 'api.access.capability.roles.delete', // Ruta asociada al permiso
                    ],
                    'capability_role_view' => [
                        'name' => 'Ver capacidad de rol',
                        'type' => 'access',
                        'route' => 'api.access.capability.roles.view', // Ruta asociada al permiso
                    ],
                    'capability_roles_list' => [
                        'name' => 'Listar capacidad de roles',
                        'type' => 'access',
                        'route' => 'api.access.capability.roles.list', // Ruta asociada al permiso
                    ]
                ]
            ]
        ]
    ]
];

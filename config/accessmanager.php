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
            // Users
            'users' => [
                'name' => 'Usuarios',
                'permissions' => [
                    'create_user' => [
                        'name' => 'Crear usuario',
                        'type' => 'action',
                        'route' => 'users.create', // Ruta asociada al permiso
                    ],
                    'update_user' => [
                        'name' => 'Actualizar usuario',
                        'type' => 'action',
                        'route' => 'users.update', // Ruta asociada al permiso
                    ],
                    'delete_user' => [
                        'name' => 'Eliminar usuario',
                        'type' => 'action',
                        'route' => 'users.delete', // Ruta asociada al permiso
                    ],
                    'view_user' => [
                        'name' => 'Ver usuario',
                        'type' => 'access',
                        'route' => 'users.view', // Ruta asociada al permiso
                    ],
                    'list_users' => [
                        'name' => 'Listar usuarios',
                        'type' => 'access',
                        'route' => 'users.list', // Ruta asociada al permiso
                    ],
                ]
            ],

            // Capability roles
            'capability_roles' => [
                'name' => 'Roles de capacidad',
                'permissions' => [
                    'create_capability_role' => [
                        'name' => 'Crear rol de capacidad',
                        'type' => 'action',
                        'route' => 'capability_roles.create', // Ruta asociada al permiso
                    ],
                    'update_capability_role' => [
                        'name' => 'Actualizar rol de capacidad',
                        'type' => 'action',
                        'route' => 'capability_roles.update', // Ruta asociada al permiso
                    ],
                    'delete_capability_role' => [
                        'name' => 'Eliminar rol de capacidad',
                        'type' => 'action',
                        'route' => 'capability_roles.delete', // Ruta asociada al permiso
                    ],
                    'view_capability_role' => [
                        'name' => 'Ver rol de capacidad',
                        'type' => 'access',
                        'route' => 'capability_roles.view', // Ruta asociada al permiso
                    ],
                    'list_capability_roles' => [
                        'name' => 'Listar roles de capacidad',
                        'type' => 'access',
                        'route' => 'capability_roles.list', // Ruta asociada al permiso
                    ],
                ]
            ]
        ]
    ]
];

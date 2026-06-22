<?php

namespace LechugaNegra\AccessManager\Database\Seeders;

use Illuminate\Database\Seeder;
use LechugaNegra\AccessManager\Models\CapabilityModule;
use LechugaNegra\AccessManager\Models\CapabilityPermission;
use LechugaNegra\AccessManager\Models\CapabilityRoute;

class AccessManagerSeeder extends Seeder
{
    /**
     * Ejecuta el seeder, carga la configuración y crea los módulos, permisos y rutas.
     *
     * @return void
     */
    public function run()
    {
        // Cargar los archivos de configuración de los módulos y custom
        $syncDefinitions = $this->systemDefinitions();
        $syncCustom = config('accessmanager_seeders.modules');

        // Unir ambos archivos de configuración
        $sync = array_merge($syncDefinitions, $syncCustom);

        // Recorrer la configuración y crear los módulos, permisos y rutas
        foreach ($sync as $keyModule => $moduleData) {
            $this->createModuleAndPermissions($keyModule, $moduleData);
        }
    }

    /**
     * Crea un módulo, sus permisos y las rutas asociadas.
     *
     * @param string $keyModule
     * @param array $moduleData
     * @return void
     */
    private function createModuleAndPermissions(string $keyModule, array $moduleData)
    {
        // Crear el módulo en la base de datos
        $moduleCode = $this->formatMachineKey($keyModule);
        $module = CapabilityModule::updateOrCreate(
            ['code' => $moduleCode],
            ['name' => $moduleData['name']]
        );

        // Crear los permisos asociados al módulo
        foreach ($moduleData['permissions'] as $permissionKey => $permissionData) {
            $permissionCode = $this->formatMachineKey($permissionKey);
    
            // Crear los permisos
            $permission = CapabilityPermission::firstOrCreate(
                ['code' => $permissionCode],
                [
                    'name' => $permissionData['name'],
                    'type' => strtolower($permissionData['type']),
                    'hidden' => ! empty($permissionData['hidden']) ? true : false,
                    'capability_module_id' => $module->id,
                ]
            );
    
            // Creación y asociación de rutas
            if (!empty($permissionData['routes'])) {
                $routeIds = collect($permissionData['routes'])->map(function ($route) {
                    $route = CapabilityRoute::firstOrCreate(['path' => $route]);
                    return $route->id;
                })->toArray();
    
                // Relación muchos a muchos entre permiso y ruta
                if (config('accessmanager.strict_sync')) {
                    $permission->routes()->sync($routeIds);
                } else {
                    $permission->routes()->syncWithoutDetaching($routeIds);
                }
            }
        }
    }

    /**
     * Arreglo de módulos, permisos y rutas.
     *
     * @return array
     */
    private function systemDefinitions(): array
    {
        return [
            // Capability roles
            'capability_roles' => [
                'name' => 'Capacidad de roles',
                'permissions' => [
                    'capability_role_create' => [
                        'name' => 'Crear capacidad de rol',
                        'type' => 'action',
                        'routes' => [
                            'api.access.capability.roles.store'
                        ]
                    ],
                    'capability_role_update' => [
                        'name' => 'Actualizar capacidad de rol',
                        'type' => 'action',
                        'routes' => [
                            'api.access.capability.roles.update'
                        ]
                    ],
                    'capability_role_delete' => [
                        'name' => 'Eliminar capacidad de rol',
                        'type' => 'action',
                        'routes' => [
                            'api.access.capability.roles.destroy'
                        ]
                    ],
                    'capability_role_show' => [
                        'name' => 'Ver capacidad de rol',
                        'type' => 'access',
                        'routes' => [
                            'api.access.capability.roles.show'
                        ]
                    ],
                    'capability_role_list' => [
                        'name' => 'Listar capacidad de roles',
                        'type' => 'access',
                        'routes' => [
                            'api.access.capability.roles.index'
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Formatea el texto a formato máquina.
     *
     * @param string $text
     * @return string
     */
    private function formatMachineKey(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8'); // Todo a minúscula
        $text = str_replace('ñ', 'n', $text); // Reemplazar ñ por n
        $text = preg_replace('/[^\w.]+/u', '_', $text); // Reemplazar cualquier grupo de caracteres no "palabra" o "." por _
        $text = preg_replace('/[^a-z_.]/', '', $text); // Eliminar todo lo que no sea a-z, "." o "_"
        $text = preg_replace('/_+/', '_', $text); // Colapsar múltiples "_" en uno solo
        $text = trim($text, '_'); // Eliminar "_" inicial o final si los hay
        return trim($text);
    }
}

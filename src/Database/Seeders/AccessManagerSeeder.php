<?php

namespace LechugaNegra\AccessManager\Database\Seeders;

use Illuminate\Database\Seeder;
use Lechuganegra\AccessManager\Models\CapabilityModule;
use Lechuganegra\AccessManager\Models\CapabilityPermission;
use Lechuganegra\AccessManager\Models\CapabilityRoute;

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
        $config = config('accessmanager.seeders.modules');
        $customConfig = config('accessmanager_seeders.modules');

        // Unir ambos archivos de configuración
        $allConfig = array_merge($config, $customConfig);

        // Recorrer la configuración y crear los módulos, permisos y rutas
        foreach ($allConfig as $moduleData) {
            $this->createModuleAndPermissions($moduleData);
        }
    }

    /**
     * Crea un módulo, sus permisos y las rutas asociadas.
     *
     * @param array $moduleData
     * @return void
     */
    private function createModuleAndPermissions(array $moduleData)
    {
        // Crear el módulo en la base de datos
        $module = CapabilityModule::firstOrCreate([
            'code' => $moduleData['name'],
            'name' => $moduleData['name']
        ]);

        // Crear los permisos asociados al módulo
        foreach ($moduleData['permissions'] as $permissionKey => $permissionData) {
            $routeId = null;
    
            // Validar creación de ruta
            if (!empty($permissionData['route'])) {
                // Crear las rutas si no existen
                $route = CapabilityRoute::firstOrCreate(
                    ['path' => $permissionData['route']],
                    ['name' => $permissionData['name']]
                );
                $routeId = $route->id;
            }

            // Crear los permisos
            CapabilityPermission::firstOrCreate([
                'code' => $permissionKey,
            ], [
                'name' => $permissionData['name'],
                'type' => $permissionData['type'],
                'capability_module_id' => $module->id,
                'capability_route_id' => $routeId
            ]);
        }
    }
}

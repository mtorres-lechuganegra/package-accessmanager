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
        foreach ($allConfig as $keyModule => $moduleData) {
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
        $module = CapabilityModule::firstOrCreate([
            'code' => $this->formatMachineKey($keyModule),
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
                'code' => $this->formatMachineKey($permissionKey),
            ], [
                'name' => $permissionData['name'],
                'type' => strtolower($permissionData['type']),
                'capability_module_id' => $module->id,
                'capability_route_id' => $routeId
            ]);
        }
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
        return $text;
    }
}

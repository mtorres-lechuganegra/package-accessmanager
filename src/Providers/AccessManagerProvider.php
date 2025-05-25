<?php

namespace LechugaNegra\AccessManager\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use LechugaNegra\AccessManager\Middleware\CapabilityAccessMiddleware;

class AccessManagerProvider extends ServiceProvider
{
    /**
     * Registrar servicios del paquete, incluyendo configuración.
     *
     * @return void
     */
    public function register()
    {
        // Registrar archivo de configuración principal
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/accessmanager.php',
            'accessmanager'
        );

        // Registrar archivo de configuración de seeders
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/accessmanager_seeders.php',
            'accessmanager_seeders'
        );
    }

    /**
     * Realizar las configuraciones necesarias.
     *
     * @return void
     */
    public function boot()
    {
        // Cargar configuración predeterminada desde el paquete
        $this->publishes([
            __DIR__ . '/../../config/accessmanager.php' => config_path('accessmanager.php'),
            __DIR__ . '/../../config/accessmanager_seeders.php' => config_path('accessmanager_seeders.php'),
        ], 'accessmanager-config');

        // Registrar el middleware en el Kernel de la aplicación
        $this->app['router']->aliasMiddleware('capability.access', CapabilityAccessMiddleware::class);
        
        // Cargar rutas dinámicas según la versión especificada en config
        $this->loadVersionedRoutes();
    }

    /**
     * Carga el archivo de rutas correspondiente a la versión configurada.
     *
     * @return void
     */
    protected function loadVersionedRoutes()
    {
        $version = config('accessmanager.version', 'v1');
        $routesPath = __DIR__ . "/../Routes/{$version}/api.php";

        if (file_exists($routesPath)) {
            Route::prefix("api/v1")
                ->group($routesPath);
        } else {
            throw new \Exception("Routes file not found for version '{$version}'.");
        }
    }
}

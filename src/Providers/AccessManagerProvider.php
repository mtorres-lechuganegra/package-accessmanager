<?php

namespace LechugaNegra\AccessManager\Providers;

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

        // Cargar rutas de api.php
        $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
    }
}

<?php

namespace LechugaNegra\AccessManager\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use LechugaNegra\AccessManager\Models\CapabilityPermission;
use LechugaNegra\AccessManager\Models\CapabilityRoute;
use LechugaNegra\AccessManager\Scopes\VisiblePermissionScope;

class AccessManagerCheck extends Command
{
    protected $signature = 'accessmanager:check';

    protected $description = 'Diagnóstico del estado de permisos y rutas registradas';

    public function handle()
    {
        $this->checkPermissionsWithoutRoutes();
        $this->checkRoutesWithoutPermissions();
        $this->checkOrphanRoutes();
    }

    /**
     * Permisos sin rutas asociadas.
     */
    private function checkPermissionsWithoutRoutes(): void
    {
        $permissions = CapabilityPermission::withoutGlobalScope(VisiblePermissionScope::class)
            ->whereDoesntHave('routes')
            ->get(['id', 'code', 'group', 'name']);

        if ($permissions->isEmpty()) {
            $this->info('✔ Todos los permisos tienen al menos una ruta asociada.');
            return;
        }

        $this->warn('⚠ Permisos sin rutas asociadas (' . $permissions->count() . '):');
        $this->table(
            ['ID', 'Group', 'Code', 'Name'],
            $permissions->map(fn($p) => [$p->id, $p->group, $p->code, $p->name])->toArray()
        );
    }

    /**
     * Rutas sin permisos asociados.
     */
    private function checkRoutesWithoutPermissions(): void
    {
        $routes = CapabilityRoute::whereDoesntHave('permissions')->get(['id', 'path']);

        if ($routes->isEmpty()) {
            $this->info('✔ Todas las rutas tienen al menos un permiso asociado.');
            return;
        }

        $this->warn('⚠ Rutas sin permisos asociados (' . $routes->count() . '):');
        $this->table(
            ['ID', 'Path'],
            $routes->map(fn($r) => [$r->id, $r->path])->toArray()
        );
    }

    /**
     * Rutas registradas en BD que no existen en Laravel.
     */
    private function checkOrphanRoutes(): void
    {
        $registeredRoutes = CapabilityRoute::pluck('path');
        $laravelRoutes = collect(Route::getRoutes()->getRoutesByName())->keys();
        $orphans = $registeredRoutes->filter(fn($path) => !$laravelRoutes->contains($path));

        if ($orphans->isEmpty()) {
            $this->info('✔ Todas las rutas registradas existen en Laravel.');
            return;
        }

        $this->error('✘ Rutas registradas en BD que no existen en Laravel (' . $orphans->count() . '):');
        $this->table(
            ['Path'],
            $orphans->map(fn($r) => [$r])->toArray()
        );
    }
}

<?php

namespace LechugaNegra\AccessManager\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Ejecuta el seeder principal del paquete AccessManager.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call(AccessManagerSeeder::class);
    }
}

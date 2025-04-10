<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('relation_entity_role', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entity_id');  // ID de la entidad relacionada
            $table->string('entity_module', 100);     // Nombre del módulo de la entidad
            $table->unsignedBigInteger('capability_role_id');  // ID del rol
            $table->timestamps();
            
            // Definimos las claves foráneas
            $table->foreign('capability_role_id')->references('id')->on('capability_roles')->onDelete('cascade');

            // 'entity_id' es el ID de la entidad relacionada con el rol. Puede ser un usuario, grupo, etc.
            // 'entity_module' es el nombre del módulo o entidad en el que se asigna el rol.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relation_entity_role');
    }
};

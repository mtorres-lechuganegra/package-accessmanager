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
        Schema::create('relation_permission_route', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('capability_permission_id');
            $table->unsignedBigInteger('capability_route_id');
            $table->timestamps();
    
            // Definimos las claves foráneas
            $table->foreign('capability_permission_id')->references('id')->on('capability_permissions')->onDelete('cascade');
            $table->foreign('capability_route_id')->references('id')->on('capability_routes')->onDelete('cascade');
    
            // Aseguramos que las combinaciones de role_id y permission_id sean únicas, con un nombre corto para el índice
            $table->unique(['capability_permission_id', 'capability_route_id'], 'permission_route_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relation_permission_route');
    }
};

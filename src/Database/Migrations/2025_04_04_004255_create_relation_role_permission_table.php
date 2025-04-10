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
        Schema::create('relation_role_permission', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('capability_role_id');
            $table->unsignedBigInteger('capability_permission_id');
            $table->timestamps();
    
            // Definimos las claves foráneas
            $table->foreign('capability_role_id')->references('id')->on('capability_roles')->onDelete('cascade');
            $table->foreign('capability_permission_id')->references('id')->on('capability_permissions')->onDelete('cascade');
    
            // Aseguramos que las combinaciones de role_id y permission_id sean únicas, con un nombre corto para el índice
            $table->unique(['capability_role_id', 'capability_permission_id'], 'role_permission_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relation_role_permission');
    }
};

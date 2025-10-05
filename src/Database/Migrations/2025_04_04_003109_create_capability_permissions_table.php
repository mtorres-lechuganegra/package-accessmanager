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
        Schema::create('capability_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->enum('type', ['access', 'action', 'manage']); // Tipo de permiso
            $table->boolean('hidden')->default(false);
            $table->timestamps();
        
            // Definimos las claves foráneas
            $table->foreignId('capability_module_id')->constrained('capability_modules')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capability_permissions');
    }
};

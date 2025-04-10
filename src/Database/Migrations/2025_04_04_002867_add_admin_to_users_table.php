<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Ejecuta la migración.
     */
    public function up(): void
    {
        Schema::table(config('accessmanager.user_entity.table'), function (Blueprint $table) {
            $table->boolean('admin')->default(false)->after('password');
        });
    }

    /**
     * Revierte la migración.
     */
    public function down(): void
    {
        Schema::table(config('accessmanager.user_entity.table'), function (Blueprint $table) {
            $table->dropColumn('admin');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('relation_entity_role', function (Blueprint $table) {
            $table->renameColumn('entity_module', 'entity_type');
        });
    }

    public function down(): void
    {
        Schema::table('relation_entity_role', function (Blueprint $table) {
            $table->renameColumn('entity_type', 'entity_module');
        });
    }
};

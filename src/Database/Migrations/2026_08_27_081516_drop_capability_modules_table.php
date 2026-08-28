<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Migrar datos: copiar code de capability_modules a group en capability_permissions
        DB::statement('
            UPDATE capability_permissions cp
            JOIN capability_modules cm ON cm.id = cp.capability_module_id
            SET cp.group = cm.code
            WHERE cp.capability_module_id IS NOT NULL
        ');

        Schema::table('capability_permissions', function ($table) {
            if (Schema::hasColumn('capability_permissions', 'capability_module_id')) {
                $table->dropForeign(['capability_module_id']);
                $table->dropColumn('capability_module_id');
            }
        });

        Schema::dropIfExists('capability_modules');
    }

    public function down(): void
    {
        Schema::create('capability_modules', function ($table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('capability_permissions', function ($table) {
            $table->unsignedBigInteger('capability_module_id')->nullable()->after('id');
            $table->foreign('capability_module_id')->references('id')->on('capability_modules')->onDelete('set null');
        });
    }
};

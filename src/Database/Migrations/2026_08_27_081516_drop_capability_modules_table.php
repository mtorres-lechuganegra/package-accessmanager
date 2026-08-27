<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('capability_permissions', function ($table) {
            if (Schema::hasColumn('capability_permissions', 'module_id')) {
                $table->dropForeign(['module_id']);
                $table->dropColumn('module_id');
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
            $table->unsignedBigInteger('module_id')->nullable()->after('id');
            $table->foreign('module_id')->references('id')->on('capability_modules')->onDelete('set null');
        });
    }
};

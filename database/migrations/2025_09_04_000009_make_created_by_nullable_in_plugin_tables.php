<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ideas
        if (Schema::hasTable('ideas') && Schema::hasColumn('ideas', 'created_by')) {
            Schema::table('ideas', function (Blueprint $table) {
                $table->foreignId('created_by')->nullable()->change();
            });
        }

        // Events
        if (Schema::hasTable('events') && Schema::hasColumn('events', 'created_by')) {
            Schema::table('events', function (Blueprint $table) {
                $table->foreignId('created_by')->nullable()->change();
            });
        }

        // Projects
        if (Schema::hasTable('custom_projects') && Schema::hasColumn('custom_projects', 'created_by')) {
            Schema::table('custom_projects', function (Blueprint $table) {
                $table->foreignId('created_by')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // Revert to non-nullable where possible
        if (Schema::hasTable('ideas') && Schema::hasColumn('ideas', 'created_by')) {
            Schema::table('ideas', function (Blueprint $table) {
                $table->foreignId('created_by')->nullable(false)->change();
            });
        }

        if (Schema::hasTable('events') && Schema::hasColumn('events', 'created_by')) {
            Schema::table('events', function (Blueprint $table) {
                $table->foreignId('created_by')->nullable(false)->change();
            });
        }

        if (Schema::hasTable('custom_projects') && Schema::hasColumn('custom_projects', 'created_by')) {
            Schema::table('custom_projects', function (Blueprint $table) {
                $table->foreignId('created_by')->nullable(false)->change();
            });
        }
    }
};



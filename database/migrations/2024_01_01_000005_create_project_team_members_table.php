<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projects_id')->constrained('custom_projects')->cascadeOnDelete();
            $table->foreignId('people_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['member', 'lead', 'manager', 'contributor'])->default('member');
            $table->timestamps();

            $table->unique(['projects_id', 'people_id'], 'unique_project_person');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_team_members');
    }
};
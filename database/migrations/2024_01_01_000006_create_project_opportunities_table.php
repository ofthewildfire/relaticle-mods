<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_opportunities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projects_id')->constrained('custom_projects')->cascadeOnDelete();
            $table->foreignId('opportunity_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['projects_id', 'opportunity_id'], 'unique_project_opportunity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_opportunities');
    }
};
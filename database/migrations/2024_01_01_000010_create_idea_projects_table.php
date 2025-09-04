<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('idea_projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idea_id');
            $table->unsignedBigInteger('projects_id');
            $table->timestamps();

            $table->foreign('idea_id')->references('id')->on('ideas')->onDelete('cascade');
            $table->foreign('projects_id')->references('id')->on('custom_projects')->onDelete('cascade');
            $table->unique(['idea_id', 'projects_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('idea_projects');
    }
};
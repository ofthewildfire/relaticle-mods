<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('idea_people', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idea_id');
            $table->unsignedBigInteger('people_id');
            $table->timestamps();

            $table->foreign('idea_id')->references('id')->on('ideas')->onDelete('cascade');
            $table->foreign('people_id')->references('id')->on('people')->onDelete('cascade');
            $table->unique(['idea_id', 'people_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('idea_people');
    }
};
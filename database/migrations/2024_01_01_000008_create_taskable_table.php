<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taskables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->morphs('taskable');
            $table->timestamps();

            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->unique(['task_id', 'taskable_id', 'taskable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taskables');
    }
};
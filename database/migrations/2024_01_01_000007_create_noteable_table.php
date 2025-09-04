<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('noteables')) {
            Schema::create('noteables', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('note_id');
                $table->morphs('noteable');
                $table->timestamps();

                $table->foreign('note_id')->references('id')->on('notes')->onDelete('cascade');
                $table->unique(['note_id', 'noteable_id', 'noteable_type']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('noteables');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ideaables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ideas_id')->constrained('ideas')->cascadeOnDelete();
            $table->morphs('ideaable');
            $table->timestamps();

            $table->unique(['ideas_id', 'ideaable_id', 'ideaable_type'], 'unique_ideaable');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ideaables');
    }
};



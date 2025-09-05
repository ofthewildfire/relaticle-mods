<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // public function up(): void
    // {
    //     Schema::create('ideas', function (Blueprint $table) {
    //         $table->id();
    //         $table->text('content');
    //         $table->enum('creation_source', ['web', 'api', 'import'])->default('web');
    //         $table->foreignId('team_id')->constrained()->cascadeOnDelete();
    //         $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    //         $table->timestamps();
    //         $table->softDeletes();
    //     });
    // }

    public function up(): void
    {
        Schema::create('ideas', function (Blueprint $table) {
            $table->id();
            $table->string('idea_name');
            $table->text('content')->nullable();
            $table->string('status')->nullable();
            $table->date('date')->nullable();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ideas');
    }
};



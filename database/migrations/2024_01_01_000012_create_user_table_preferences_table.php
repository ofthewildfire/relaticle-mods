<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_table_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('resource_name'); // e.g., 'ideas', 'projects', 'events'
            $table->json('preferences'); // Store column visibility, sort, filters, etc.
            $table->timestamps();
            
            // Ensure one preference record per user per resource
            $table->unique(['user_id', 'resource_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_table_preferences');
    }
};

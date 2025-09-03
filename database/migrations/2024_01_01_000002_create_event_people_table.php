<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_people', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('people_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['organizer', 'attendee', 'speaker', 'sponsor', 'volunteer'])->default('attendee');
            $table->timestamps();

            $table->unique(['event_id', 'people_id'], 'unique_event_person');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_people');
    }
};
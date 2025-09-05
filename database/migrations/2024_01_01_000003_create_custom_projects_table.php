<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // public function up(): void
    // {
    //     Schema::create('custom_projects', function (Blueprint $table) {
    //         $table->id();
    //         $table->string('project_name');
    //         $table->text('description')->nullable();
    //         $table->decimal('budget', 15, 2)->nullable();
    //         $table->date('start_date');
    //         $table->date('end_date')->nullable();
    //         $table->enum('status', ['planning', 'active', 'on_hold', 'completed', 'cancelled'])->default('planning');
    //         $table->boolean('is_priority')->default(false);
    //         $table->string('contact_email')->nullable();
    //         $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
    //         $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
    //         $table->foreignId('team_id')->constrained()->cascadeOnDelete();
    //         $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    //         $table->timestamps();
    //         $table->softDeletes();

    //         $table->index(['status', 'is_priority']);
    //         $table->index(['start_date', 'end_date']);
    //     });
    // }

    public function up(): void
{
    Schema::create('custom_projects', function (Blueprint $table) {
        $table->id();
        $table->string('project_name');
        $table->text('description')->nullable();
        $table->string('status')->nullable();
        $table->foreignId('team_id')->constrained()->cascadeOnDelete();
        $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamps();
        $table->softDeletes();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('custom_projects');
    }
};
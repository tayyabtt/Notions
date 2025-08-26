<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_trackers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ['not_started', 'in_progress', 'complete'])->default('not_started');
            $table->string('assignee')->nullable();
            $table->date('due_date')->nullable();
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('task_type', ['polish', 'feature_request', 'bug', 'enhancement', 'documentation'])->default('feature_request');
            $table->enum('effort_level', ['small', 'medium', 'large'])->default('medium');
            $table->foreignId('team_id')->nullable()->constrained('teams')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_trackers');
    }
};
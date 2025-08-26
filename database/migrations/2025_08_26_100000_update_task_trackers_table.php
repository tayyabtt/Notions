<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_trackers', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['assignee_id']);
            // Drop the old column
            $table->dropColumn('assignee_id');
            // Add the new column
            $table->string('assignee')->nullable();
            // Update status enum values
            $table->dropColumn('status');
        });
        
        Schema::table('task_trackers', function (Blueprint $table) {
            $table->enum('status', ['not_started', 'in_progress', 'complete'])->default('not_started');
        });
    }

    public function down(): void
    {
        Schema::table('task_trackers', function (Blueprint $table) {
            $table->dropColumn(['assignee', 'status']);
            $table->foreignId('assignee_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['in_progress', 'done'])->default('in_progress');
        });
    }
};
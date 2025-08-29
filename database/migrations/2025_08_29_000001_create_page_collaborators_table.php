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
        Schema::create('page_collaborators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('task_tracker_page_id')->constrained()->onDelete('cascade');
            $table->enum('permission_level', ['view', 'edit', 'owner'])->default('view');
            $table->timestamps();

            $table->unique(['user_id', 'task_tracker_page_id']);
            $table->index(['task_tracker_page_id', 'permission_level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_collaborators');
    }
};
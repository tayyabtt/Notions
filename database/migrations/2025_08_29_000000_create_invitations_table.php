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
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->foreignId('task_tracker_page_id')->constrained()->onDelete('cascade');
            $table->string('token')->unique();
            $table->enum('permission_level', ['view', 'edit'])->default('view');
            $table->enum('status', ['pending', 'accepted', 'expired'])->default('pending');
            $table->foreignId('invited_by_user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['email', 'status']);
            $table->index(['token', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
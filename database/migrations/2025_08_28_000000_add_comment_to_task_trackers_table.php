<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_trackers', function (Blueprint $table) {
            $table->text('comment')->nullable();
            $table->string('comment_file_name')->nullable();
            $table->string('comment_file_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('task_trackers', function (Blueprint $table) {
            $table->dropColumn(['comment', 'comment_file_name', 'comment_file_path']);
        });
    }
};
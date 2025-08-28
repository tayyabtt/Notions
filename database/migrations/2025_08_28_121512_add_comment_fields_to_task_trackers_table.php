<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_trackers', function (Blueprint $table) {
            if (!Schema::hasColumn('task_trackers', 'comment')) {
                $table->text('comment')->nullable();
            }
            if (!Schema::hasColumn('task_trackers', 'comment_file_name')) {
                $table->string('comment_file_name')->nullable();
            }
            if (!Schema::hasColumn('task_trackers', 'comment_file_path')) {
                $table->string('comment_file_path')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('task_trackers', function (Blueprint $table) {
            if (Schema::hasColumn('task_trackers', 'comment')) {
                $table->dropColumn('comment');
            }
            if (Schema::hasColumn('task_trackers', 'comment_file_name')) {
                $table->dropColumn('comment_file_name');
            }
            if (Schema::hasColumn('task_trackers', 'comment_file_path')) {
                $table->dropColumn('comment_file_path');
            }
        });
    }
};
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
        Schema::table('task_trackers', function (Blueprint $table) {
            $table->string('subtask_1')->nullable()->after('comment_file_path');
            $table->string('subtask_2')->nullable()->after('subtask_1');
            $table->string('subtask_3')->nullable()->after('subtask_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_trackers', function (Blueprint $table) {
            $table->dropColumn(['subtask_1', 'subtask_2', 'subtask_3']);
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_trackers', function (Blueprint $table) {
            $table->unsignedBigInteger('page_id')->nullable()->after('team_id');
            $table->foreign('page_id')->references('id')->on('task_tracker_pages')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('task_trackers', function (Blueprint $table) {
            $table->dropForeign(['page_id']);
            $table->dropColumn('page_id');
        });
    }
};
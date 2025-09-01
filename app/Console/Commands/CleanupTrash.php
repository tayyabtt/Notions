<?php

namespace App\Console\Commands;

use App\Models\TaskTracker;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupTrash extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trash:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete tasks that have been in trash for more than 30 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        
        $this->info("Cleaning up tasks deleted before: {$thirtyDaysAgo->toDateTimeString()}");
        
        $oldTasks = TaskTracker::onlyTrashed()
            ->where('deleted_at', '<', $thirtyDaysAgo)
            ->get();

        $count = $oldTasks->count();
        
        if ($count === 0) {
            $this->info('No tasks found that are older than 30 days.');
            return 0;
        }

        $this->info("Found {$count} tasks to permanently delete...");
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($oldTasks as $task) {
            // Delete associated files if they exist
            if ($task->comment_file_path) {
                Storage::disk('public')->delete($task->comment_file_path);
            }
            
            $task->forceDelete();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully cleaned up {$count} tasks from trash.");
        
        return 0;
    }
}
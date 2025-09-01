<?php

namespace App\Http\Controllers;

use App\Models\TaskTracker;
use App\Models\TaskTrackerPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TrashController extends Controller
{
    public function index(Request $request)
    {
        // Get all task tracker pages the user has access to
        $ownedPages = Auth::user()->taskTrackerPages;
        $sharedPages = Auth::user()->collaboratedPages;
        $taskTrackerPages = $ownedPages->merge($sharedPages);

        // Get deleted tasks from accessible pages only
        $pageIds = $taskTrackerPages->pluck('id');
        
        $deletedTasks = TaskTracker::onlyTrashed()
            ->whereIn('page_id', $pageIds)
            ->orWhere(function($query) {
                // Include tasks without page_id that belong to user's teams
                $query->whereNull('page_id')
                      ->where('created_by', Auth::id());
            })
            ->with(['creator', 'page'])
            ->orderBy('deleted_at', 'desc')
            ->get();

        // Group by how long ago they were deleted
        $groupedTasks = [
            'today' => [],
            'this_week' => [],
            'this_month' => [],
            'older' => []
        ];

        foreach ($deletedTasks as $task) {
            $deletedAt = Carbon::parse($task->deleted_at);
            $now = Carbon::now();

            if ($deletedAt->isToday()) {
                $groupedTasks['today'][] = $task;
            } elseif ($deletedAt->isCurrentWeek()) {
                $groupedTasks['this_week'][] = $task;
            } elseif ($deletedAt->isCurrentMonth()) {
                $groupedTasks['this_month'][] = $task;
            } else {
                $groupedTasks['older'][] = $task;
            }
        }

        return view('trash.index', compact('deletedTasks', 'groupedTasks', 'taskTrackerPages'));
    }

    public function restore(TaskTracker $task)
    {
        
        // Check if user has permission to restore this task
        if ($task->page_id) {
            $page = $task->page;
            if (!$page->canUserEdit(Auth::user())) {
                abort(403, 'You do not have permission to restore this task.');
            }
        } else {
            // For tasks without page, only creator can restore
            if ($task->created_by !== Auth::id()) {
                abort(403, 'You can only restore tasks you created.');
            }
        }

        $task->restore();

        return redirect()->back()->with('success', 'Task restored successfully!');
    }

    public function forceDelete(TaskTracker $task)
    {
        
        // Check if user has permission to permanently delete this task
        if ($task->page_id) {
            $page = $task->page;
            if (!$page->canUserEdit(Auth::user())) {
                abort(403, 'You do not have permission to permanently delete this task.');
            }
        } else {
            // For tasks without page, only creator can permanently delete
            if ($task->created_by !== Auth::id()) {
                abort(403, 'You can only permanently delete tasks you created.');
            }
        }

        // Delete associated files if they exist
        if ($task->comment_file_path) {
            \Storage::disk('public')->delete($task->comment_file_path);
        }

        $task->forceDelete();

        return redirect()->back()->with('success', 'Task permanently deleted!');
    }

    public function emptyTrash()
    {
        // Get all task tracker pages the user has access to
        $ownedPages = Auth::user()->taskTrackerPages;
        $sharedPages = Auth::user()->collaboratedPages;
        $taskTrackerPages = $ownedPages->merge($sharedPages);
        $pageIds = $taskTrackerPages->pluck('id');

        // Get all deleted tasks user can manage
        $deletedTasks = TaskTracker::onlyTrashed()
            ->where(function($query) use ($pageIds) {
                $query->whereIn('page_id', $pageIds)
                      ->orWhere(function($q) {
                          $q->whereNull('page_id')->where('created_by', Auth::id());
                      });
            })
            ->get();

        // Delete associated files and permanently delete tasks
        foreach ($deletedTasks as $task) {
            if ($task->comment_file_path) {
                \Storage::disk('public')->delete($task->comment_file_path);
            }
            $task->forceDelete();
        }

        return redirect()->back()->with('success', 'Trash emptied successfully!');
    }

    public function cleanup()
    {
        // This method will be called by a scheduled job to clean up tasks older than 30 days
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        
        $oldTasks = TaskTracker::onlyTrashed()
            ->where('deleted_at', '<', $thirtyDaysAgo)
            ->get();

        foreach ($oldTasks as $task) {
            if ($task->comment_file_path) {
                \Storage::disk('public')->delete($task->comment_file_path);
            }
            $task->forceDelete();
        }

        return "Cleaned up " . $oldTasks->count() . " tasks older than 30 days.";
    }
}
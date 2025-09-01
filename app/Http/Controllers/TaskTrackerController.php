<?php

namespace App\Http\Controllers;

use App\Models\TaskTracker;
use App\Models\TaskTrackerPage;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskTrackerController extends Controller
{
    public function index(Request $request)
    {
        $teams = Auth::user()->teams;
        $taskTrackerPages = Auth::user()->taskTrackerPages;
        $currentTeam = null;
        $view = $request->get('view', 'all'); // all, my_tasks, by_status

        if ($request->has('team') && $request->team) {
            $currentTeam = Team::findOrFail($request->team);
            $query = TaskTracker::where('team_id', $currentTeam->id)
                ->with(['creator', 'team']);
        } else {
            // Show all task trackers when no specific team or page is selected
            $query = TaskTracker::with(['creator', 'team'])
                ->whereNull('page_id');
        }

        // Apply view filters
        if ($view === 'my_tasks') {
            $query->where('assignee', Auth::user()->name);
        }

        $taskTrackers = $query->orderBy('created_at', 'desc')->get();

        // Group by status if requested
        $groupedTasks = null;
        if ($view === 'by_status') {
            $groupedTasks = $taskTrackers->groupBy('status');
        }

        // Return JSON response for AJAX requests
        if ($request->has('ajax') && $request->ajax == '1') {
            // Prepare task data with additional properties needed for display
            $taskData = $taskTrackers->map(function ($task) {
                return [
                    'id' => $task->id,
                    'name' => $task->name,
                    'description' => $task->description,
                    'status' => $task->status,
                    'assignee' => $task->assignee,
                    'due_date' => $task->due_date ? $task->due_date->format('m/d/Y') : null,
                    'priority' => $task->priority,
                    'task_type' => $task->task_type,
                    'task_type_icon' => $this->getTaskTypeIcon($task->task_type),
                    'effort_level' => $task->effort_level,
                ];
            });

            return response()->json([
                'taskTrackers' => $taskData,
                'groupedTasks' => $groupedTasks ? $groupedTasks->map(function ($group) {
                    return $group->map(function ($task) {
                        return [
                            'id' => $task->id,
                            'name' => $task->name,
                            'description' => $task->description,
                            'status' => $task->status,
                            'assignee' => $task->assignee,
                            'due_date' => $task->due_date ? $task->due_date->format('m/d/Y') : null,
                            'priority' => $task->priority,
                            'task_type' => $task->task_type,
                            'task_type_icon' => $this->getTaskTypeIcon($task->task_type),
                            'effort_level' => $task->effort_level,
                        ];
                    });
                }) : null,
                'view' => $view,
                'currentTeam' => $currentTeam,
            ]);
        }

        return view('task-tracker.index', compact('teams', 'currentTeam', 'taskTrackers', 'taskTrackerPages', 'view', 'groupedTasks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:not_started,in_progress,complete',
            'assignee' => 'nullable|string|max:255',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
            'task_type' => 'required|in:polish,feature_request,bug,enhancement,documentation',
            'effort_level' => 'required|in:small,medium,large',
            'team_id' => 'nullable|exists:teams,id',
            'page_id' => 'nullable|exists:task_tracker_pages,id',
        ]);

        // Check permissions if creating task on a specific page
        if ($request->page_id) {
            $page = TaskTrackerPage::findOrFail($request->page_id);
            if (!$page->canUserEdit(Auth::user())) {
                abort(403, 'You do not have permission to create tasks on this page.');
            }
        }

        TaskTracker::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
            'assignee' => $request->assignee,
            'due_date' => $request->due_date,
            'priority' => $request->priority,
            'task_type' => $request->task_type,
            'effort_level' => $request->effort_level,
            'team_id' => $request->team_id,
            'page_id' => $request->page_id,
            'created_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Task tracker item created successfully!');
    }

    public function update(Request $request, TaskTracker $taskTracker)
    {
        // Check permissions if task belongs to a specific page
        if ($taskTracker->page_id) {
            $page = $taskTracker->page;
            if (!$page->canUserEdit(Auth::user())) {
                abort(403, 'You do not have permission to edit tasks on this page.');
            }
        }

        // Build validation rules only for fields that are present
        $validationRules = [];
        
        if ($request->has('name')) {
            $validationRules['name'] = 'required|string|max:255';
        }
        if ($request->has('description')) {
            $validationRules['description'] = 'nullable|string';
        }
        if ($request->has('status')) {
            $validationRules['status'] = 'required|in:not_started,in_progress,complete';
        }
        if ($request->has('assignee')) {
            $validationRules['assignee'] = 'nullable|string|max:255';
        }
        if ($request->has('due_date')) {
            $validationRules['due_date'] = 'nullable|date';
        }
        if ($request->has('priority')) {
            $validationRules['priority'] = 'required|in:low,medium,high';
        }
        if ($request->has('task_type')) {
            $validationRules['task_type'] = 'required|in:polish,feature_request,bug,enhancement,documentation';
        }
        if ($request->has('effort_level')) {
            $validationRules['effort_level'] = 'required|in:small,medium,large';
        }
        if ($request->has('comment')) {
            $validationRules['comment'] = 'nullable|string';
        }
        if ($request->hasFile('comment_file')) {
            $validationRules['comment_file'] = 'nullable|file|max:10240'; // 10MB max
        }
        if ($request->has('subtask_1')) {
            $validationRules['subtask_1'] = 'nullable|string|max:255';
        }
        if ($request->has('subtask_2')) {
            $validationRules['subtask_2'] = 'nullable|string|max:255';
        }
        if ($request->has('subtask_3')) {
            $validationRules['subtask_3'] = 'nullable|string|max:255';
        }
        
        $request->validate($validationRules);

        // Only update fields that are present in the request
        $fieldsToUpdate = $request->only(array_keys($validationRules));

        // Handle file upload if present
        if ($request->hasFile('comment_file')) {
            $file = $request->file('comment_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('comment_files', $fileName, 'public');
            
            $fieldsToUpdate['comment_file_name'] = $file->getClientOriginalName();
            $fieldsToUpdate['comment_file_path'] = $filePath;
            
            // Remove the file from fieldsToUpdate as it's not a database field
            unset($fieldsToUpdate['comment_file']);
        }

        $taskTracker->update($fieldsToUpdate);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Task updated successfully']);
        }

        return redirect()->back()->with('success', 'Task tracker item updated successfully!');
    }

    public function destroy(TaskTracker $taskTracker)
    {
        // Check permissions if task belongs to a specific page
        if ($taskTracker->page_id) {
            $page = $taskTracker->page;
            if (!$page->canUserEdit(Auth::user())) {
                abort(403, 'You do not have permission to delete tasks on this page.');
            }
        }

        $taskTracker->delete();
        return redirect()->back()->with('success', 'Task tracker item deleted successfully!');
    }

    private function getTaskTypeIcon($taskType)
    {
        $icons = [
            'polish' => '✨',
            'feature_request' => '💡',
            'bug' => '🐛',
            'enhancement' => '🚀',
            'documentation' => '📝',
        ];

        return $icons[$taskType] ?? '';
    }
}
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
        
        $request->validate($validationRules);

        // Only update fields that are present in the request
        $fieldsToUpdate = $request->only(array_keys($validationRules));

        $taskTracker->update($fieldsToUpdate);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Task updated successfully']);
        }

        return redirect()->back()->with('success', 'Task tracker item updated successfully!');
    }

    public function destroy(TaskTracker $taskTracker)
    {
        $taskTracker->delete();
        return redirect()->back()->with('success', 'Task tracker item deleted successfully!');
    }
}
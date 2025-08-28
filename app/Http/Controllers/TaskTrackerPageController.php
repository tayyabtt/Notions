<?php

namespace App\Http\Controllers;

use App\Models\TaskTrackerPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskTrackerPageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:10',
        ]);

        $page = TaskTrackerPage::create([
            'name' => $request->name ?: 'Untitled',
            'description' => $request->description,
            'icon' => $request->icon ?: '✅',
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('task-tracker-page.show', $page)
                         ->with('success', 'Task tracker page created successfully!');
    }

    public function quickStore(Request $request)
    {
        $page = TaskTrackerPage::create([
            'name' => 'Tasks Trackers',
            'description' => 'Stay organized with tasks, your way.',
            'icon' => '✅',
            'user_id' => Auth::id(),
        ]);

        // Create 3 default tasks
        $defaultTasks = [
            [
                'name' => 'improve website copy',
                'description' => 'Update and enhance website content for better user experience',
                'status' => 'in_progress',
                'assignee' => Auth::user()->name,
                'due_date' => now()->addDays(7),
                'priority' => 'high',
                'task_type' => 'enhancement',
                'effort_level' => 'medium',
                'page_id' => $page->id,
                'created_by' => Auth::id(),
            ],
            [
                'name' => 'update help centers and faq',
                'description' => 'Revise help center content and frequently asked questions',
                'status' => 'in_progress',
                'assignee' => Auth::user()->name,
                'due_date' => now()->addDays(10),
                'priority' => 'medium',
                'task_type' => 'documentation',
                'effort_level' => 'large',
                'page_id' => $page->id,
                'created_by' => Auth::id(),
            ],
            [
                'name' => 'publish release notes',
                'description' => 'Create and publish comprehensive release notes for latest updates',
                'status' => 'in_progress',
                'assignee' => Auth::user()->name,
                'due_date' => now()->addDays(5),
                'priority' => 'high',
                'task_type' => 'documentation',
                'effort_level' => 'small',
                'page_id' => $page->id,
                'created_by' => Auth::id(),
            ],
        ];

        foreach ($defaultTasks as $taskData) {
            \App\Models\TaskTracker::create($taskData);
        }

        return redirect()->route('task-tracker-page.show', $page)
                         ->with('success', 'Task tracker page created successfully!');
    }

    public function show(Request $request, TaskTrackerPage $page)
    {
        $view = $request->get('view', 'all');
        
        $taskTrackersQuery = $page->taskTrackers()
            ->with(['creator'])
            ->orderBy('created_at', 'desc');

        if ($view === 'my_tasks') {
            $taskTrackersQuery->where('assignee', Auth::user()->name)
                             ->orWhere('creator_id', Auth::id());
        }

        $taskTrackers = $taskTrackersQuery->get();
        
        $groupedTasks = null;
        if ($view === 'by_status') {
            $groupedTasks = $taskTrackers->groupBy('status');
        }

        $taskTrackerPages = Auth::user()->taskTrackerPages;

        return view('task-tracker.page', compact('page', 'taskTrackers', 'taskTrackerPages', 'view', 'groupedTasks'));
    }

    public function update(Request $request, TaskTrackerPage $page)
    {
        $validationRules = [];
        
        if ($request->has('name')) {
            $validationRules['name'] = 'required|string|max:255';
        }
        if ($request->has('description')) {
            $validationRules['description'] = 'nullable|string';
        }
        if ($request->has('icon')) {
            $validationRules['icon'] = 'nullable|string|max:10';
        }
        
        $request->validate($validationRules);

        $fieldsToUpdate = $request->only(array_keys($validationRules));
        $page->update($fieldsToUpdate);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Page updated successfully']);
        }

        return redirect()->back()->with('success', 'Task tracker page updated successfully!');
    }

    public function destroy(TaskTrackerPage $page)
    {
        $page->delete();
        return redirect()->route('task-tracker.index')->with('success', 'Task tracker page deleted successfully!');
    }
}
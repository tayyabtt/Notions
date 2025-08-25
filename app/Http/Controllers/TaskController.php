<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tasks = Task::whereHas('team.users', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->with(['assignee', 'creator', 'team', 'tags'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'tasks' => $tasks
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'in:low,medium,high',
            'status' => 'in:todo,in_progress,done',
            'due_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
            'team_id' => 'required|exists:teams,id',
        ]);

        // Check if user is team member
        $team = Team::findOrFail($request->team_id);
        if (!$team->users()->where('user_id', $request->user()->id)->exists()) {
            return back()->withErrors(['error' => 'Access denied']);
        }

        Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority ?? 'medium',
            'status' => $request->status ?? 'todo',
            'due_date' => $request->due_date,
            'team_id' => $team->id,
            'created_by' => $request->user()->id,
            'assigned_to' => $request->assigned_to,
        ]);

        return redirect()->route('teams.show', $team->id)->with('success', 'Task created successfully!');
    }

    public function show(Task $task, Request $request): JsonResponse
    {
        // Check if user has access to this task
        if (!$task->team->users()->where('user_id', $request->user()->id)->exists()) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        return response()->json([
            'task' => $task->load(['assignee', 'creator', 'team', 'comments.user', 'tags'])
        ]);
    }

    public function update(Request $request, Task $task)
    {
        // Check if user has access to this task
        if (!$task->team->users()->where('user_id', $request->user()->id)->exists()) {
            return back()->withErrors(['error' => 'Access denied']);
        }

        $request->validate([
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'priority' => 'in:low,medium,high',
            'status' => 'in:todo,in_progress,done',
            'due_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $task->update($request->only([
            'title', 'description', 'priority', 'status', 'due_date', 'assigned_to'
        ]));

        return redirect()->route('teams.show', $task->team_id)->with('success', 'Task updated successfully!');
    }

    public function destroy(Task $task, Request $request)
    {
        // Check if user has access to this task
        if (!$task->team->users()->where('user_id', $request->user()->id)->exists()) {
            return back()->withErrors(['error' => 'Access denied']);
        }

        $teamId = $task->team_id;
        $task->delete();

        return redirect()->route('teams.show', $teamId)->with('success', 'Task deleted successfully!');
    }

    public function addComment(Request $request, Task $task): JsonResponse
    {
        // Check if user has access to this task
        if (!$task->team->users()->where('user_id', $request->user()->id)->exists()) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $request->validate([
            'content' => 'required|string',
            'mentioned_users' => 'nullable|array',
            'mentioned_users.*' => 'exists:users,id'
        ]);

        $comment = $task->comments()->create([
            'content' => $request->content,
            'user_id' => $request->user()->id,
            'mentioned_users' => $request->mentioned_users,
        ]);

        return response()->json([
            'message' => 'Comment added successfully',
            'comment' => $comment->load('user')
        ], 201);
    }

    public function getTeamMembers(Task $task, Request $request): JsonResponse
    {
        // Check if user has access to this task
        if (!$task->team->users()->where('user_id', $request->user()->id)->exists()) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $teamMembers = $task->team->users()->select('id', 'name', 'email')->get();

        return response()->json([
            'team_members' => $teamMembers
        ]);
    }
}
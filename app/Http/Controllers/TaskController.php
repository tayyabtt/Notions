<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use App\Services\NotificationService;
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

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority ?? 'medium',
            'status' => $request->status ?? 'todo',
            'due_date' => $request->due_date,
            'team_id' => $team->id,
            'created_by' => $request->user()->id,
            'assigned_to' => $request->assigned_to,
        ]);

        // Send notification if task is assigned to someone
        if ($task->assigned_to && $task->assigned_to !== $request->user()->id) {
            $assignee = User::find($task->assigned_to);
            if ($assignee) {
                $notificationService = app(NotificationService::class);
                $notificationService->notifyTaskAssigned($task, $assignee, $request->user());
            }
        }

        return redirect()->route('teams.show', $team->id)->with('success', 'Task created successfully!');
    }

    public function show(Task $task, Request $request)
    {
        // Check if user has access to this task
        if (!$task->team->users()->where('user_id', $request->user()->id)->exists()) {
            return back()->withErrors(['error' => 'Access denied']);
        }

        $task = $task->load(['assignee', 'creator', 'team', 'comments.user', 'tags']);
        
        return view('task-detail', compact('task'));
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

        // Store old values for notifications
        $oldStatus = $task->status;
        $oldAssignee = $task->assigned_to;
        
        $task->update($request->only([
            'title', 'description', 'priority', 'status', 'due_date', 'assigned_to'
        ]));
        
        $notificationService = app(NotificationService::class);
        
        // Notify about status changes
        if ($request->filled('status') && $oldStatus !== $request->status) {
            $notificationService->notifyTaskStatusUpdated($task, $request->user(), $oldStatus, $request->status);
        }
        
        // Notify about new assignment
        if ($request->filled('assigned_to') && $oldAssignee !== $request->assigned_to && $request->assigned_to !== $request->user()->id) {
            $assignee = User::find($request->assigned_to);
            if ($assignee) {
                $notificationService->notifyTaskAssigned($task, $assignee, $request->user());
            }
        }

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

    public function addComment(Request $request, Task $task)
    {
        // Check if user has access to this task
        if (!$task->team->users()->where('user_id', $request->user()->id)->exists()) {
            return back()->withErrors(['error' => 'Access denied']);
        }

        $request->validate([
            'content' => 'required|string',
        ]);

        // Parse @mentions from content
        $content = $request->content;
        $mentionedUsers = $this->parseMentions($content, $task->team);
        
        // Process mentions in content to create links
        $processedContent = $this->processMentions($content, $task->team);

        $comment = $task->comments()->create([
            'content' => $processedContent,
            'user_id' => $request->user()->id,
            'mentioned_users' => $mentionedUsers,
        ]);

        $notificationService = app(NotificationService::class);
        
        // Send notifications for mentions
        if (!empty($mentionedUsers)) {
            $notificationService->notifyMention($comment, $mentionedUsers);
        }
        
        // Send general comment notification to task creator and assignee
        $notificationService->notifyTaskComment($comment);

        return redirect()->route('tasks.show', $task->id)->with('success', 'Comment added successfully!');
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

    /**
     * Parse @mentions from comment content
     */
    private function parseMentions(string $content, Team $team): array
    {
        $mentionedUsers = [];
        
        // Match @username patterns
        preg_match_all('/@(\w+)/', $content, $matches);
        
        if (!empty($matches[1])) {
            $usernames = $matches[1];
            
            // Find users in the team by name (simplified - in real app you'd have usernames)
            $users = $team->users()->get();
            
            foreach ($usernames as $username) {
                $user = $users->first(function ($user) use ($username) {
                    return stripos($user->name, $username) !== false;
                });
                
                if ($user && !in_array($user->id, $mentionedUsers)) {
                    $mentionedUsers[] = $user->id;
                }
            }
        }
        
        return $mentionedUsers;
    }

    /**
     * Process @mentions in content to create visual links
     */
    private function processMentions(string $content, Team $team): string
    {
        $users = $team->users()->get();
        
        // Replace @mentions with styled spans (Notion-style)
        $processedContent = preg_replace_callback('/@(\w+)/', function ($matches) use ($users) {
            $username = $matches[1];
            
            $user = $users->first(function ($user) use ($username) {
                return stripos($user->name, $username) !== false;
            });
            
            if ($user) {
                return '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">@' . $user->name . '</span>';
            }
            
            return $matches[0]; // Return original if user not found
        }, $content);
        
        return $processedContent;
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Team;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'color' => 'required|string|max:7',
            'team_id' => 'required|exists:teams,id',
        ]);

        // Check if user is team member
        $team = Team::findOrFail($request->team_id);
        if (!$team->users()->where('user_id', $request->user()->id)->exists()) {
            return back()->withErrors(['error' => 'Access denied']);
        }

        // Check if tag name already exists for this team
        $existingTag = Tag::where('team_id', $team->id)
                          ->where('name', $request->name)
                          ->first();
        
        if ($existingTag) {
            return back()->withErrors(['error' => 'Tag already exists']);
        }

        Tag::create([
            'name' => $request->name,
            'color' => $request->color,
            'team_id' => $team->id,
        ]);

        return redirect()->route('teams.show', $team->id)->with('success', 'Tag created successfully!');
    }

    public function destroy(Tag $tag, Request $request)
    {
        // Check if user is team member
        if (!$tag->team->users()->where('user_id', $request->user()->id)->exists()) {
            return back()->withErrors(['error' => 'Access denied']);
        }

        $teamId = $tag->team_id;
        $tag->delete();

        return redirect()->route('teams.show', $teamId)->with('success', 'Tag deleted successfully!');
    }

    public function attachToTask(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'tag_id' => 'required|exists:tags,id',
        ]);

        $task = \App\Models\Task::findOrFail($request->task_id);
        $tag = Tag::findOrFail($request->tag_id);

        // Check if user has access to this task
        if (!$task->team->users()->where('user_id', $request->user()->id)->exists()) {
            return back()->withErrors(['error' => 'Access denied']);
        }

        // Check if tag belongs to the same team as task
        if ($task->team_id !== $tag->team_id) {
            return back()->withErrors(['error' => 'Tag does not belong to task team']);
        }

        // Attach tag to task if not already attached
        if (!$task->tags->contains($tag->id)) {
            $task->tags()->attach($tag->id);
        }

        return redirect()->route('teams.show', $task->team_id)->with('success', 'Tag added to task!');
    }

    public function detachFromTask(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'tag_id' => 'required|exists:tags,id',
        ]);

        $task = \App\Models\Task::findOrFail($request->task_id);

        // Check if user has access to this task
        if (!$task->team->users()->where('user_id', $request->user()->id)->exists()) {
            return back()->withErrors(['error' => 'Access denied']);
        }

        $task->tags()->detach($request->tag_id);

        return redirect()->route('teams.show', $task->team_id)->with('success', 'Tag removed from task!');
    }
}
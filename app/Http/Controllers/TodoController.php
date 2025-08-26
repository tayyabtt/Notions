<?php

namespace App\Http\Controllers;

use App\Models\TodoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TodoController extends Controller
{
    public function index(Request $request)
    {
        $view = $request->get('view', 'todo');
        
        $query = auth()->user()->todoItems();
        
        if ($view === 'done') {
            $todoItems = $query->where('is_completed', true)->orderBy('completed_at', 'desc')->get();
        } else {
            $todoItems = $query->where('is_completed', false)->orderBy('created_at', 'desc')->get();
        }
        
        return view('todo', compact('todoItems', 'view'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        try {
            auth()->user()->todoItems()->create([
                'title' => $request->title,
                'is_completed' => false
            ]);

            return redirect()->route('todo.index')->with('success', 'Todo item added successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to add todo item']);
        }
    }

    public function toggle(Request $request, TodoItem $todoItem)
    {
        // Check if user owns this todo item
        if ($todoItem->user_id !== auth()->id()) {
            return back()->withErrors(['error' => 'Access denied']);
        }

        try {
            $todoItem->update([
                'is_completed' => !$todoItem->is_completed,
                'completed_at' => !$todoItem->is_completed ? now() : null
            ]);

            return redirect()->route('todo.index', ['view' => request('view', 'todo')])
                           ->with('success', 'Todo item updated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update todo item']);
        }
    }

    public function destroy(TodoItem $todoItem)
    {
        // Check if user owns this todo item
        if ($todoItem->user_id !== auth()->id()) {
            return back()->withErrors(['error' => 'Access denied']);
        }

        try {
            $todoItem->delete();
            return redirect()->route('todo.index', ['view' => request('view', 'todo')])
                           ->with('success', 'Todo item deleted successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete todo item']);
        }
    }
}
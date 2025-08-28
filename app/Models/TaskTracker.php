<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskTracker extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
        'assignee',
        'due_date',
        'priority',
        'task_type',
        'effort_level',
        'team_id',
        'page_id',
        'created_by',
        'comment',
        'comment_file_name',
        'comment_file_path',
        'subtask_1',
        'subtask_2',
        'subtask_3',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(TaskTrackerPage::class);
    }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'low' => '#10B981',
            'medium' => '#F59E0B',
            'high' => '#EF4444',
            default => '#6B7280',
        };
    }

    public function getTaskTypeColorAttribute(): string
    {
        return match ($this->task_type) {
            'polish' => '#EC4899',
            'feature_request' => '#3B82F6',
            'bug' => '#EF4444',
            'enhancement' => '#8B5CF6',
            'documentation' => '#10B981',
            default => '#6B7280',
        };
    }

    public function getTaskTypeIconAttribute(): string
    {
        return match ($this->task_type) {
            'polish' => '✨',
            'feature_request' => '💡',
            'bug' => '🐛',
            'enhancement' => '🚀',
            'documentation' => '📝',
            default => '📋',
        };
    }
}
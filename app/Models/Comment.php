<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    protected $fillable = [
        'content',
        'task_id',
        'user_id',
        'mentioned_users',
    ];

    protected $casts = [
        'mentioned_users' => 'array',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mentionedUsers()
    {
        if (!$this->mentioned_users) {
            return collect();
        }
        
        return User::whereIn('id', $this->mentioned_users)->get();
    }
}

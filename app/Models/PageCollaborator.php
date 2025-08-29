<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageCollaborator extends Model
{
    protected $fillable = [
        'user_id',
        'task_tracker_page_id',
        'permission_level',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function taskTrackerPage(): BelongsTo
    {
        return $this->belongsTo(TaskTrackerPage::class);
    }

    public function canEdit(): bool
    {
        return in_array($this->permission_level, ['edit', 'owner']);
    }

    public function canView(): bool
    {
        return in_array($this->permission_level, ['view', 'edit', 'owner']);
    }

    public function isOwner(): bool
    {
        return $this->permission_level === 'owner';
    }

    public function scopeWithPermission($query, string $permission)
    {
        return $query->where('permission_level', $permission);
    }
}
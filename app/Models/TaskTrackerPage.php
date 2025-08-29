<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskTrackerPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'user_id',
        'icon',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function taskTrackers(): HasMany
    {
        return $this->hasMany(TaskTracker::class, 'page_id');
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    public function collaborators(): HasMany
    {
        return $this->hasMany(PageCollaborator::class);
    }

    public function isOwner(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    public function hasCollaborator(User $user): bool
    {
        return $this->collaborators()->where('user_id', $user->id)->exists();
    }

    public function getCollaboratorPermission(User $user): ?string
    {
        $collaborator = $this->collaborators()->where('user_id', $user->id)->first();
        return $collaborator?->permission_level;
    }

    public function canUserAccess(User $user): bool
    {
        return $this->isOwner($user) || $this->hasCollaborator($user);
    }

    public function canUserEdit(User $user): bool
    {
        if ($this->isOwner($user)) {
            return true;
        }

        $permission = $this->getCollaboratorPermission($user);
        return in_array($permission, ['edit', 'owner']);
    }
}
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
}
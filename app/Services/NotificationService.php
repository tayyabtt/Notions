<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Send task assignment notification
     */
    public function notifyTaskAssigned(Task $task, User $assignee, User $assignedBy)
    {
        $title = "You've been assigned to a task";
        $message = "{$assignedBy->name} assigned you to \"{$task->title}\" in {$task->team->name}";
        
        $notification = $this->createNotification(
            $assignee,
            'task_assigned',
            $title,
            $message,
            $task,
            [
                'task_id' => $task->id,
                'task_title' => $task->title,
                'team_name' => $task->team->name,
                'assigned_by' => $assignedBy->name,
            ]
        );

        // Send email notification
        $this->sendEmailNotification($assignee, $title, $message, [
            'task' => $task,
            'assigned_by' => $assignedBy,
            'notification_type' => 'task_assigned',
        ]);

        return $notification;
    }

    /**
     * Send task status update notification
     */
    public function notifyTaskStatusUpdated(Task $task, User $updatedBy, string $oldStatus, string $newStatus)
    {
        $title = "Task status updated";
        $message = "{$updatedBy->name} moved \"{$task->title}\" from " . ucfirst(str_replace('_', ' ', $oldStatus)) . " to " . ucfirst(str_replace('_', ' ', $newStatus));
        
        // Notify task creator and assignee (if different from updater)
        $usersToNotify = collect([$task->creator, $task->assignee])
            ->filter()
            ->unique('id')
            ->reject(function ($user) use ($updatedBy) {
                return $user->id === $updatedBy->id;
            });

        foreach ($usersToNotify as $user) {
            $notification = $this->createNotification(
                $user,
                'task_status_updated',
                $title,
                $message,
                $task,
                [
                    'task_id' => $task->id,
                    'task_title' => $task->title,
                    'team_name' => $task->team->name,
                    'updated_by' => $updatedBy->name,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                ]
            );

            $this->sendEmailNotification($user, $title, $message, [
                'task' => $task,
                'updated_by' => $updatedBy,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'notification_type' => 'task_status_updated',
            ]);
        }
    }

    /**
     * Send mention notification
     */
    public function notifyMention(Comment $comment, array $mentionedUserIds)
    {
        $mentionedUsers = User::whereIn('id', $mentionedUserIds)->get();
        
        foreach ($mentionedUsers as $user) {
            $title = "You were mentioned in a comment";
            $message = "{$comment->user->name} mentioned you in a comment on \"{$comment->task->title}\"";
            
            $notification = $this->createNotification(
                $user,
                'mention',
                $title,
                $message,
                $comment,
                [
                    'comment_id' => $comment->id,
                    'task_id' => $comment->task->id,
                    'task_title' => $comment->task->title,
                    'team_name' => $comment->task->team->name,
                    'mentioned_by' => $comment->user->name,
                    'comment_content' => $comment->content,
                ]
            );

            $this->sendEmailNotification($user, $title, $message, [
                'comment' => $comment,
                'task' => $comment->task,
                'mentioned_by' => $comment->user,
                'notification_type' => 'mention',
            ]);
        }
    }

    /**
     * Send task comment notification
     */
    public function notifyTaskComment(Comment $comment)
    {
        $task = $comment->task;
        $title = "New comment on task";
        $message = "{$comment->user->name} commented on \"{$task->title}\"";
        
        // Notify task creator and assignee (if different from commenter)
        $usersToNotify = collect([$task->creator, $task->assignee])
            ->filter()
            ->unique('id')
            ->reject(function ($user) use ($comment) {
                return $user->id === $comment->user->id;
            });

        foreach ($usersToNotify as $user) {
            $notification = $this->createNotification(
                $user,
                'task_comment',
                $title,
                $message,
                $comment,
                [
                    'comment_id' => $comment->id,
                    'task_id' => $task->id,
                    'task_title' => $task->title,
                    'team_name' => $task->team->name,
                    'commented_by' => $comment->user->name,
                    'comment_content' => $comment->content,
                ]
            );

            $this->sendEmailNotification($user, $title, $message, [
                'comment' => $comment,
                'task' => $task,
                'commented_by' => $comment->user,
                'notification_type' => 'task_comment',
            ]);
        }
    }

    /**
     * Create a notification record
     */
    private function createNotification(User $user, string $type, string $title, string $message, $notifiable = null, array $data = []): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'notifiable_type' => $notifiable ? get_class($notifiable) : null,
            'notifiable_id' => $notifiable ? $notifiable->id : null,
        ]);
    }

    /**
     * Send email notification
     */
    private function sendEmailNotification(User $user, string $title, string $message, array $data = [])
    {
        try {
            Mail::send('emails.notification', [
                'user' => $user,
                'title' => $title,
                'message' => $message,
                'data' => $data,
            ], function ($mail) use ($user, $title) {
                $mail->to($user->email, $user->name)
                     ->subject($title . ' - ' . config('app.name'));
            });
        } catch (\Exception $e) {
            // Log email sending failure
            \Log::error('Failed to send email notification: ' . $e->getMessage());
        }
    }
}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #3b82f6;
        }
        .content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        .button {
            display: inline-block;
            background: #3b82f6;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .task-info {
            background: #f9fafb;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0; color: #1f2937;">{{ $title }}</h1>
        <p style="margin: 5px 0 0 0; color: #6b7280;">{{ config('app.name') }} Notification</p>
    </div>

    <div class="content">
        <p>Hi {{ $user->name }},</p>
        
        <p>{{ $message }}</p>

        @if(isset($data['notification_type']))
            @if($data['notification_type'] === 'task_assigned')
                <div class="task-info">
                    <h3 style="margin: 0 0 10px 0;">Task Details:</h3>
                    <p><strong>Task:</strong> {{ $data['task']->title }}</p>
                    <p><strong>Team:</strong> {{ $data['task']->team->name }}</p>
                    <p><strong>Priority:</strong> {{ ucfirst($data['task']->priority) }}</p>
                    @if($data['task']->due_date)
                        <p><strong>Due Date:</strong> {{ $data['task']->due_date->format('M j, Y') }}</p>
                    @endif
                    <p><strong>Assigned by:</strong> {{ $data['assigned_by']->name }}</p>
                </div>

                <a href="{{ route('tasks.show', $data['task']->id) }}" class="button">View Task</a>

            @elseif($data['notification_type'] === 'task_status_updated')
                <div class="task-info">
                    <h3 style="margin: 0 0 10px 0;">Status Update:</h3>
                    <p><strong>Task:</strong> {{ $data['task']->title }}</p>
                    <p><strong>Team:</strong> {{ $data['task']->team->name }}</p>
                    <p><strong>Status changed:</strong> {{ ucfirst(str_replace('_', ' ', $data['old_status'])) }} → {{ ucfirst(str_replace('_', ' ', $data['new_status'])) }}</p>
                    <p><strong>Updated by:</strong> {{ $data['updated_by']->name }}</p>
                </div>

                <a href="{{ route('tasks.show', $data['task']->id) }}" class="button">View Task</a>

            @elseif($data['notification_type'] === 'mention')
                <div class="task-info">
                    <h3 style="margin: 0 0 10px 0;">You were mentioned:</h3>
                    <p><strong>Task:</strong> {{ $data['task']->title }}</p>
                    <p><strong>Team:</strong> {{ $data['task']->team->name }}</p>
                    <p><strong>Mentioned by:</strong> {{ $data['mentioned_by']->name }}</p>
                    <p><strong>Comment:</strong></p>
                    <div style="background: #f3f4f6; padding: 10px; border-radius: 4px; font-style: italic;">
                        {!! nl2br($data['comment']->content) !!}
                    </div>
                </div>

                <a href="{{ route('tasks.show', $data['task']->id) }}" class="button">View Comment</a>

            @elseif($data['notification_type'] === 'task_comment')
                <div class="task-info">
                    <h3 style="margin: 0 0 10px 0;">New Comment:</h3>
                    <p><strong>Task:</strong> {{ $data['task']->title }}</p>
                    <p><strong>Team:</strong> {{ $data['task']->team->name }}</p>
                    <p><strong>Comment by:</strong> {{ $data['commented_by']->name }}</p>
                    <p><strong>Comment:</strong></p>
                    <div style="background: #f3f4f6; padding: 10px; border-radius: 4px;">
                        {!! nl2br($data['comment']->content) !!}
                    </div>
                </div>

                <a href="{{ route('tasks.show', $data['task']->id) }}" class="button">View Task</a>
            @endif
        @endif

        <p>This notification was sent because you're part of the {{ isset($data['task']) ? $data['task']->team->name : 'team' }} workspace in {{ config('app.name') }}.</p>
    </div>

    <div class="footer">
        <p>Best regards,<br>The {{ config('app.name') }} Team</p>
        <p>
            <small>
                You're receiving this email because you're a member of a team workspace. 
                To manage your notification preferences, visit your profile settings.
            </small>
        </p>
    </div>
</body>
</html>
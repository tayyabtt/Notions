<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Tracker Invitation</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            line-height: 1.6;
            color: #37352f;
            background-color: #ffffff;
            margin: 0;
            padding: 40px 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e5e5e3;
            border-radius: 8px;
            overflow: hidden;
        }
        .header {
            background: #f7f7f5;
            padding: 30px;
            text-align: center;
            border-bottom: 1px solid #e5e5e3;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
            color: #37352f;
        }
        .content {
            padding: 30px;
        }
        .page-info {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
            display: flex;
            align-items: center;
        }
        .page-icon {
            font-size: 32px;
            margin-right: 15px;
        }
        .page-details h3 {
            margin: 0 0 5px 0;
            font-size: 18px;
            color: #37352f;
        }
        .page-details p {
            margin: 0;
            color: #787774;
            font-size: 14px;
        }
        .invite-button {
            display: inline-block;
            background: #0074E4;
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 16px;
            margin: 20px 0;
            transition: background-color 0.2s;
        }
        .invite-button:hover {
            background: #0056B3;
            color: white;
            text-decoration: none;
        }
        .permission-info {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
        }
        .footer {
            background: #f7f7f5;
            padding: 20px 30px;
            border-top: 1px solid #e5e5e3;
            text-align: center;
            color: #787774;
            font-size: 14px;
        }
        .footer p {
            margin: 5px 0;
        }
        .link {
            word-break: break-all;
            color: #0074E4;
            font-size: 14px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Task Tracker Invitation</h1>
        </div>
        
        <div class="content">
            <p>Hello!</p>
            
            <p><strong>{{ $invitation->invitedBy->name }}</strong> has invited you to collaborate on their task tracker page.</p>
            
            <div class="page-info">
                <div class="page-icon">{{ $invitation->taskTrackerPage->icon }}</div>
                <div class="page-details">
                    <h3>{{ $invitation->taskTrackerPage->name }}</h3>
                    <p>{{ $invitation->taskTrackerPage->description ?: 'Stay organized with tasks, your way.' }}</p>
                </div>
            </div>

            <div class="permission-info">
                <strong>🔐 Access Level:</strong> {{ ucfirst($invitation->permission_level) }} Access
                @if($invitation->permission_level === 'view')
                    <br><small>You'll be able to view tasks and comments but not make changes.</small>
                @else
                    <br><small>You'll be able to view and edit tasks, add comments, and manage content.</small>
                @endif
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ url('/accept-invite/' . $invitation->token) }}" class="invite-button">
                    Accept Invitation
                </a>
            </div>

            <p>This invitation will expire on <strong>{{ $invitation->expires_at->format('M d, Y \a\t h:i A') }}</strong>.</p>

            <div class="link">
                <p>If the button doesn't work, copy and paste this link into your browser:</p>
                <p>{{ url('/accept-invite/' . $invitation->token) }}</p>
            </div>
        </div>
        
        <div class="footer">
            <p>This invitation was sent from {{ config('app.name') }}</p>
            <p>If you didn't expect this invitation, you can safely ignore this email.</p>
        </div>
    </div>
</body>
</html>
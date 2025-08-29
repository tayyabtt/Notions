<?php

namespace App\Http\Controllers;

use App\Mail\TaskTrackerInvitation;
use App\Models\Invitation;
use App\Models\PageCollaborator;
use App\Models\TaskTrackerPage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    public function sendInvitation(Request $request, TaskTrackerPage $page)
    {
        $request->validate([
            'email' => 'required|email',
            'permission_level' => 'required|in:view,edit',
        ]);

        // Check if user is the page owner
        if (!$page->isOwner(auth()->user())) {
            return back()->withErrors(['error' => 'Only page owners can send invitations.']);
        }

        // Check if user is already a collaborator
        $existingCollaborator = PageCollaborator::where('task_tracker_page_id', $page->id)
            ->whereHas('user', function ($query) use ($request) {
                $query->where('email', $request->email);
            })
            ->first();

        if ($existingCollaborator) {
            return back()->withErrors(['error' => 'This user is already a collaborator on this page.']);
        }

        // Check if there's already a pending invitation
        $existingInvitation = Invitation::where('email', $request->email)
            ->where('task_tracker_page_id', $page->id)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if ($existingInvitation) {
            return back()->withErrors(['error' => 'An invitation has already been sent to this email address.']);
        }

        // Create invitation
        $invitation = Invitation::create([
            'email' => $request->email,
            'task_tracker_page_id' => $page->id,
            'token' => Invitation::generateToken(),
            'permission_level' => $request->permission_level,
            'invited_by_user_id' => auth()->id(),
            'expires_at' => now()->addWeek(),
        ]);

        // Send email
        try {
            Mail::to($request->email)->send(new TaskTrackerInvitation($invitation));
            
            return back()->with('success', 'Invitation sent successfully to ' . $request->email);
        } catch (\Exception $e) {
            $invitation->delete();
            return back()->withErrors(['error' => 'Failed to send invitation. Please check email configuration.']);
        }
    }

    public function acceptInvitation($token)
    {
        $invitation = Invitation::where('token', $token)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if (!$invitation) {
            return redirect()->route('login')->withErrors(['error' => 'Invalid or expired invitation.']);
        }

        // Check if user exists
        $user = User::where('email', $invitation->email)->first();
        
        if (!$user) {
            // Create new user account
            $user = User::create([
                'name' => explode('@', $invitation->email)[0], // Use email prefix as name
                'email' => $invitation->email,
                'password' => Hash::make(Str::random(16)), // Random password, user will need to reset
                'email_verified_at' => now(),
            ]);
        }

        // Check if user is already a collaborator
        $existingCollaborator = PageCollaborator::where('user_id', $user->id)
            ->where('task_tracker_page_id', $invitation->task_tracker_page_id)
            ->first();

        if (!$existingCollaborator) {
            // Add user as collaborator
            PageCollaborator::create([
                'user_id' => $user->id,
                'task_tracker_page_id' => $invitation->task_tracker_page_id,
                'permission_level' => $invitation->permission_level,
            ]);
        }

        // Mark invitation as accepted
        $invitation->update(['status' => 'accepted']);

        // Log the user in if they weren't already logged in
        if (!auth()->check()) {
            auth()->login($user);
        }

        return redirect()->route('task-tracker-page.show', $invitation->taskTrackerPage)
            ->with('success', 'Welcome! You now have access to this task tracker page.');
    }

    public function revokeInvitation(Invitation $invitation)
    {
        $page = $invitation->taskTrackerPage;
        
        if (!$page->isOwner(auth()->user())) {
            return back()->withErrors(['error' => 'Only page owners can revoke invitations.']);
        }

        $invitation->update(['status' => 'expired']);

        return back()->with('success', 'Invitation revoked successfully.');
    }

    public function removeCollaborator(TaskTrackerPage $page, PageCollaborator $collaborator)
    {
        if (!$page->isOwner(auth()->user())) {
            return back()->withErrors(['error' => 'Only page owners can remove collaborators.']);
        }

        if ($collaborator->isOwner()) {
            return back()->withErrors(['error' => 'Cannot remove the page owner.']);
        }

        $collaborator->delete();

        return back()->with('success', 'Collaborator removed successfully.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Http\Requests\Team\CreateTeamRequest;
use App\Http\Requests\Team\InviteTeamMemberRequest;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $teams = $request->user()->teams()->with(['owner', 'users'])->get();
        
        return response()->json([
            'teams' => $teams
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        DB::beginTransaction();
        
        try {
            $team = Team::create([
                'name' => $request->name,
                'description' => $request->description,
                'owner_id' => $request->user()->id,
                'invite_code' => Str::random(10),
            ]);

            // Add creator as admin
            $team->users()->attach($request->user()->id, [
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('teams.show', $team->id)->with('success', 'Team created successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create team']);
        }
    }

    public function show(Team $team, Request $request): JsonResponse
    {
        // Check if user is team member
        if (!$team->users()->where('user_id', $request->user()->id)->exists()) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $team->load(['owner', 'users', 'tasks', 'tags']);
        
        return response()->json([
            'team' => $team
        ]);
    }

    public function update(CreateTeamRequest $request, Team $team): JsonResponse
    {
        // Check if user is team admin
        $userRole = $team->users()->where('user_id', $request->user()->id)->first();
        
        if (!$userRole || $userRole->pivot->role !== 'admin') {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $team->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Team updated successfully',
            'team' => $team->fresh(['owner', 'users'])
        ]);
    }

    public function destroy(Team $team, Request $request): JsonResponse
    {
        // Only team owner can delete team
        if ($team->owner_id !== $request->user()->id) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $team->delete();

        return response()->json([
            'message' => 'Team deleted successfully'
        ]);
    }

    public function inviteMember(InviteTeamMemberRequest $request, Team $team): JsonResponse
    {
        // Check if user is team admin
        $userRole = $team->users()->where('user_id', $request->user()->id)->first();
        
        if (!$userRole || $userRole->pivot->role !== 'admin') {
            return response()->json(['message' => 'Access denied'], 403);
        }

        // Check if user is already a team member
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser && $team->users()->where('user_id', $existingUser->id)->exists()) {
            return response()->json(['message' => 'User is already a team member'], 400);
        }

        // Check for existing pending invitation
        $existingInvitation = TeamInvitation::where('team_id', $team->id)
            ->where('email', $request->email)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if ($existingInvitation) {
            return response()->json(['message' => 'Invitation already sent'], 400);
        }

        // Create invitation
        $invitation = TeamInvitation::create([
            'email' => $request->email,
            'team_id' => $team->id,
            'invited_by' => $request->user()->id,
            'token' => Str::random(32),
            'expires_at' => now()->addDays(7),
        ]);

        // TODO: Send email invitation (we'll implement this later)
        
        return response()->json([
            'message' => 'Invitation sent successfully',
            'invitation' => $invitation
        ], 201);
    }

    public function removeMember(Team $team, User $user, Request $request): JsonResponse
    {
        // Check if current user is team admin
        $userRole = $team->users()->where('user_id', $request->user()->id)->first();
        
        if (!$userRole || $userRole->pivot->role !== 'admin') {
            return response()->json(['message' => 'Access denied'], 403);
        }

        // Cannot remove team owner
        if ($team->owner_id === $user->id) {
            return response()->json(['message' => 'Cannot remove team owner'], 400);
        }

        // Remove user from team
        $team->users()->detach($user->id);

        return response()->json([
            'message' => 'Member removed successfully'
        ]);
    }

    public function joinByInviteCode(Request $request): JsonResponse
    {
        $request->validate([
            'invite_code' => 'required|string'
        ]);

        $team = Team::where('invite_code', $request->invite_code)->first();
        
        if (!$team) {
            return response()->json(['message' => 'Invalid invite code'], 404);
        }

        // Check if already member
        if ($team->users()->where('user_id', $request->user()->id)->exists()) {
            return response()->json(['message' => 'You are already a member'], 400);
        }

        // Add user as member
        $team->users()->attach($request->user()->id, [
            'role' => 'member',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Successfully joined team',
            'team' => $team->load(['owner', 'users'])
        ]);
    }
}
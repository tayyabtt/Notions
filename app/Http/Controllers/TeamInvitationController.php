<?php

namespace App\Http\Controllers;

use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeamInvitationController extends Controller
{
    public function accept(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        $invitation = TeamInvitation::where('token', $request->token)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if (!$invitation) {
            return response()->json([
                'message' => 'Invalid or expired invitation'
            ], 404);
        }

        DB::beginTransaction();
        
        try {
            $user = $request->user();
            
            // Check if invitation email matches user's email
            if ($invitation->email !== $user->email) {
                return response()->json([
                    'message' => 'Invitation email does not match your account'
                ], 400);
            }

            // Check if already member
            if ($invitation->team->users()->where('user_id', $user->id)->exists()) {
                $invitation->update(['status' => 'accepted']);
                DB::commit();
                
                return response()->json([
                    'message' => 'You are already a member of this team'
                ], 400);
            }

            // Add user to team
            $invitation->team->users()->attach($user->id, [
                'role' => 'member',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Mark invitation as accepted
            $invitation->update(['status' => 'accepted']);
            
            DB::commit();

            return response()->json([
                'message' => 'Successfully joined team',
                'team' => $invitation->team->load(['owner', 'users'])
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to accept invitation'
            ], 500);
        }
    }

    public function reject(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        $invitation = TeamInvitation::where('token', $request->token)
            ->where('status', 'pending')
            ->first();

        if (!$invitation) {
            return response()->json([
                'message' => 'Invalid invitation'
            ], 404);
        }

        $invitation->update(['status' => 'rejected']);

        return response()->json([
            'message' => 'Invitation rejected'
        ]);
    }

    public function show(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        $invitation = TeamInvitation::where('token', $request->token)
            ->with(['team', 'inviter'])
            ->first();

        if (!$invitation) {
            return response()->json([
                'message' => 'Invalid invitation'
            ], 404);
        }

        return response()->json([
            'invitation' => [
                'id' => $invitation->id,
                'team_name' => $invitation->team->name,
                'team_description' => $invitation->team->description,
                'inviter_name' => $invitation->inviter->name,
                'status' => $invitation->status,
                'expires_at' => $invitation->expires_at,
                'is_expired' => $invitation->isExpired(),
            ]
        ]);
    }

    public function myInvitations(Request $request): JsonResponse
    {
        $invitations = TeamInvitation::where('email', $request->user()->email)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->with(['team', 'inviter'])
            ->get();

        return response()->json([
            'invitations' => $invitations
        ]);
    }
}
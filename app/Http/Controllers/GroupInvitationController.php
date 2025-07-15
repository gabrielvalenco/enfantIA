<?php

namespace App\Http\Controllers;

use App\Models\GroupInvitation;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GroupInvitationController extends Controller
{
    public function index()
    {
        $pendingInvitations = Auth::user()->pendingGroupInvitations()->with('group.creator')->get();
        
        // Mark notifications as seen in the session
        session(['notifications_seen' => true]);
        session(['last_notification_count' => $pendingInvitations->count()]);
        session(['last_notification_check' => now()->timestamp]);
        
        return view('notifications.index', compact('pendingInvitations'));
    }

    public function accept(GroupInvitation $invitation)
    {
        // Check if the invitation belongs to the authenticated user
        if ($invitation->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para aceitar este convite.');
        }

        // Check if the invitation is still pending
        if ($invitation->status !== 'pending') {
            return redirect()->route('notifications.index')
                ->with('error', 'Este convite já foi respondido anteriormente.');
        }

        // Check if the group has reached the maximum number of members
        if ($invitation->group->members()->count() >= 4) {
            $invitation->update([
                'status' => 'rejected',
                'responded_at' => now()
            ]);
            
            return redirect()->route('notifications.index')
                ->with('error', 'O grupo já atingiu o limite máximo de 4 membros.');
        }

        // Accept the invitation
        $invitation->update([
            'status' => 'accepted',
            'responded_at' => now()
        ]);

        // Add the user to the group
        $invitation->group->members()->attach(Auth::id(), ['role' => 'member']);

        return redirect()->route('groups.show', $invitation->group)
            ->with('success', 'Convite aceito com sucesso! Você agora é membro do grupo.');
    }

    public function reject(GroupInvitation $invitation)
    {
        // Check if the invitation belongs to the authenticated user
        if ($invitation->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para rejeitar este convite.');
        }

        // Check if the invitation is still pending
        if ($invitation->status !== 'pending') {
            return redirect()->route('notifications.index')
                ->with('error', 'Este convite já foi respondido anteriormente.');
        }

        // Reject the invitation
        $invitation->update([
            'status' => 'rejected',
            'responded_at' => now()
        ]);

        return redirect()->route('notifications.index')
            ->with('success', 'Convite rejeitado com sucesso.');
    }
    
    public function clearAll()
    {
        // Get all pending invitations for the current user
        $pendingInvitations = Auth::user()->pendingGroupInvitations();
        
        // Mark all as rejected
        $pendingInvitations->update([
            'status' => 'rejected',
            'responded_at' => now()
        ]);
        
        // Update the session variables
        session(['notifications_seen' => true]);
        session(['last_notification_count' => 0]);
        session(['last_notification_check' => now()->timestamp]);
        
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Todas as notificações foram limpas.']);
        }
        
        return redirect()->route('notifications.index')
            ->with('success', 'Todas as notificações foram limpas.');
    }
}

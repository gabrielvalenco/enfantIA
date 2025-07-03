<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Auth::user()->groups;
        return view('groups.index', compact('groups'));
    }

    public function create()
    {
        return view('groups.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'members' => 'nullable|array|max:3',
            'members.*' => 'email|exists:users,email'
        ]);

        $group = Group::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'created_by' => Auth::id(),
        ]);

        // Add creator as admin
        $group->members()->attach(Auth::id(), ['role' => 'admin']);

        // Send invitations to other members
        $invitedMembers = [];
        if (!empty($validated['members'])) {
            foreach ($validated['members'] as $email) {
                if (!empty($email)) {
                    $user = User::where('email', $email)->first();
                    // Skip if the user is trying to invite themselves
                    if ($user && $user->id !== Auth::id()) {
                        // Check if there's already a pending invitation
                        $existingInvitation = GroupInvitation::where('group_id', $group->id)
                            ->where('user_id', $user->id)
                            ->exists();
                        
                        if (!$existingInvitation) {
                            // Create invitation
                            GroupInvitation::create([
                                'group_id' => $group->id,
                                'user_id' => $user->id,
                                'status' => 'pending'
                            ]);
                            $invitedMembers[] = $email;
                        }
                    }
                }
            }
        }

        $message = 'Grupo criado com sucesso!';
        if (count($invitedMembers) > 0) {
            $message .= ' Convites enviados para: ' . implode(', ', $invitedMembers);
        }

        return redirect()->route('groups.show', $group)
            ->with('success', $message);
    }

    public function show(Group $group)
    {
        if (!$group->isMember(Auth::user())) {
            abort(403);
        }

        $tasks = $group->tasks()->with(['creator', 'assignedUser'])->get();
        $members = $group->members()->get();

        return view('groups.show', compact('group', 'tasks', 'members'));
    }

    public function addMember(Request $request, Group $group)
    {
        if (!$group->isAdmin(Auth::user())) {
            abort(403);
        }

        $validated = $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $validated['email'])->first();

        // Prevent inviting yourself
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Você não pode convidar a si mesmo para o grupo.');
        }

        if ($group->members()->count() >= 4) {
            return back()->with('error', 'O grupo já atingiu o limite máximo de 4 membros.');
        }

        if ($group->isMember($user)) {
            return back()->with('error', 'Este usuário já é membro do grupo.');
        }

        // Check if there's already a pending invitation
        $existingInvitation = GroupInvitation::where('group_id', $group->id)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existingInvitation) {
            return back()->with('info', 'Este usuário já possui um convite pendente para este grupo.');
        }

        // Create a new invitation
        GroupInvitation::create([
            'group_id' => $group->id,
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Convite enviado com sucesso!');
    }

    public function removeMember(Request $request, Group $group)
    {
        if (!$group->isAdmin(Auth::user())) {
            abort(403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        if ($validated['user_id'] == Auth::id()) {
            return back()->with('error', 'Você não pode remover a si mesmo do grupo.');
        }

        $group->members()->detach($validated['user_id']);
        return back()->with('success', 'Membro removido com sucesso!');
    }

    public function delete(Group $group)
    {
        $group->delete();
        return redirect()->route('groups.index')->with('success', 'Grupo deletado com sucesso!');
    }

    public function destroy(Group $group)
    {
        $group->delete();
        return redirect()->route('groups.index')
            ->with('success', 'Grupo removido com sucesso!');
    }

    public function leave(Group $group)
    {
        // Remove o usuário da lista de membros do grupo
        $group->members()->detach(Auth::id());
        
        // Remove todos os convites anteriores do mesmo usuário para este grupo
        GroupInvitation::where('group_id', $group->id)
            ->where('user_id', Auth::id())
            ->delete();
        
        return redirect()->route('groups.index')
            ->with('success', 'Saída realizada com sucesso!');
    }
    
    /**
     * Salva as configurações do grupo
     *
     * @param Request $request
     * @param Group $group
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveSettings(Request $request, Group $group)
    {
        // Verifica se o usuário é administrador do grupo
        if (!$group->isAdmin(Auth::user())) {
            return response()->json(['success' => false, 'message' => 'Você não tem permissão para alterar as configurações deste grupo.'], 403);
        }

        $validated = $request->validate([
            'competitive_mode' => 'required|boolean',
            'allow_member_invite' => 'required|boolean',
            'allow_member_tasks' => 'required|boolean',
        ]);

        // Atualiza as configurações do grupo
        $group->update([
            'competitive_mode' => $validated['competitive_mode'],
            'allow_member_invite' => $validated['allow_member_invite'],
            'allow_member_tasks' => $validated['allow_member_tasks'],
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Configurações salvas com sucesso!',
            'data' => [
                'competitive_mode' => $group->competitive_mode,
                'allow_member_invite' => $group->allow_member_invite,
                'allow_member_tasks' => $group->allow_member_tasks,
            ]
        ]);
    }
    
    /**
     * Obtém as configurações do grupo
     *
     * @param Group $group
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSettings(Group $group)
    {
        // Verifica se o usuário é membro do grupo
        if (!$group->isMember(Auth::user())) {
            return response()->json(['success' => false, 'message' => 'Você não tem acesso a este grupo.'], 403);
        }
        
        // Verifica se o usuário é administrador para determinar permissões de edição
        $isAdmin = $group->isAdmin(Auth::user());
        
        // Obtém ranking de membros mais produtivos (se modo competitivo estiver ativo)
        $topMembers = [];
        if ($group->competitive_mode) {
            $topMembers = $group->members()
                ->withCount(['tasks' => function ($query) {
                    $query->where('status', true); // tarefas completas
                }])
                ->orderByDesc('tasks_count')
                ->take(3)
                ->get()
                ->map(function ($member) {
                    return [
                        'name' => $member->name,
                        'email' => $member->email,
                        'tasks_completed' => $member->tasks_count
                    ];
                });
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'can_edit' => $isAdmin,
                'competitive_mode' => $group->competitive_mode,
                'allow_member_invite' => $group->allow_member_invite,
                'allow_member_tasks' => $group->allow_member_tasks,
                'top_members' => $topMembers
            ]
        ]);
    }
}

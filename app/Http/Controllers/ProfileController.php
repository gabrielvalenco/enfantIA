<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        // Handle position field - if 'Outro' is selected, use the custom_position value
        $position = $request->position;
        if ($position === 'Outro' && $request->filled('custom_position')) {
            $position = $request->custom_position;
        }
        
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'position' => $position,
            'bio' => $request->bio,
            'preferences' => $user->preferences ?? []
        ];
        
        if ($request->hasFile('avatar')) {
            $updateData['avatar'] = $user->avatar;
        }
        
        $user->update($updateData);

        return redirect()->route('profile.index')
            ->with('success', 'Perfil atualizado com sucesso!');
    }
}

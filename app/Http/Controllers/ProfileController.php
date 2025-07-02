<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SocialLink;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $socialLinks = $user->socialLinks()->pluck('url')->toArray();
        
        // Get metrics data
        $metrics = [
            'tasks_completed' => $user->completedTasks()->count(),
            'best_streak' => 5, // Placeholder for now
            'on_time_percent' => 85, // Placeholder for now
            'projects_finished' => 12 // Placeholder for now
        ];
        
        return view('profile.index', compact('user', 'socialLinks', 'metrics'));
    }

    public function edit()
    {
        $user = auth()->user();
        $socialLinks = $user->socialLinks()->pluck('url')->toArray();
        
        return view('profile.edit', compact('user', 'socialLinks'));
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
        
        // Handle social links
        if ($request->has('social_links')) {
            // Delete existing links
            $user->socialLinks()->delete();
            
            // Add new links
            $order = 0;
            foreach ($request->social_links as $url) {
                if (!empty($url)) {
                    $user->socialLinks()->create([
                        'url' => $url,
                        'order' => $order++
                    ]);
                }
            }
        }

        return redirect()->route('profile.index')
            ->with('success', 'Perfil atualizado com sucesso!');
    }
}

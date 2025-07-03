<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Check if a user exists and if they're in the specified group
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkUser(Request $request)
    {
        $email = $request->input('email');
        $groupId = $request->input('group_id');
        
        $user = User::where('email', $email)->first();
        
        $response = [
            'exists' => false,
            'inGroup' => false
        ];
        
        if ($user) {
            $response['exists'] = true;
            
            // If a group_id is provided, check if the user is already in that group
            if ($groupId) {
                $response['inGroup'] = $user->groups()->where('groups.id', $groupId)->exists();
            }
        }
        
        return response()->json($response);
    }
}

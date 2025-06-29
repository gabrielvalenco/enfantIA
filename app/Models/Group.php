<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;
use App\Models\Task;
use App\Models\GroupInvitation;

class Group extends Model
{
    protected $fillable = ['name', 'description', 'created_by', 'competitive_mode', 'allow_member_invite', 'allow_member_tasks'];
    
    protected $casts = [
        'competitive_mode' => 'boolean',
        'allow_member_invite' => 'boolean',
        'allow_member_tasks' => 'boolean'
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_members')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function isAdmin(User $user): bool
    {
        return $this->members()
                    ->wherePivot('user_id', $user->id)
                    ->wherePivot('role', 'admin')
                    ->exists();
    }

    public function isMember(User $user): bool
    {
        return $this->members()
                    ->wherePivot('user_id', $user->id)
                    ->exists();
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(GroupInvitation::class);
    }

    public function pendingInvitations(): HasMany
    {
        return $this->hasMany(GroupInvitation::class)->where('status', 'pending');
    }
}

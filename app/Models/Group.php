<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    protected $fillable = ['name', 'description', 'created_by'];

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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Group;
use App\Models\User;

class GroupInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'user_id',
        'status',
        'responded_at'
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    // Relationship with Group
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope for pending invitations
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}

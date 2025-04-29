<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\User;
use App\Models\Group;
use App\Models\Subtask;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'due_date',
        'status',
        'urgency',
        'user_id',
        'category_id',
        'group_id',
        'assigned_to'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'status' => 'boolean',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
    
    public function subtasks()
    {
        return $this->hasMany(Subtask::class);
    }
    
    // Método para verificar se todas as subtarefas estão concluídas
    public function allSubtasksCompleted()
    {
        if ($this->subtasks->isEmpty()) {
            return true;
        }
        
        return $this->subtasks->every(function ($subtask) {
            return $subtask->completed;
        });
    }
}

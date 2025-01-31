<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Task;
use App\Models\User;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'user_id'];

    public function tasks()
    {
        return $this->belongsToMany(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

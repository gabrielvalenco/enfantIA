<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Task;
use App\Models\Category;

class Note extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content', 'user_id', 'task_id', 'category_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    /**
     * Obtém as categorias através da tarefa associada
     */
    public function getTaskCategoriesAttribute()
    {
        return $this->task ? $this->task->categories : null;
    }
}

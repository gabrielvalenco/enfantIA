<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'category_id', 'due_date', 'status'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

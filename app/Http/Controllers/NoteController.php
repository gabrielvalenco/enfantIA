<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;
use App\Models\Task;
use App\Models\Category;

class NoteController extends Controller
{
    public function index()
    {
        $notes = auth()->user()->notes()->with(['task', 'task.categories'])->orderBy('updated_at', 'desc')->get();
        $tasks = auth()->user()->tasks()->select('id', 'title')->orderBy('title')->get();
        $categories = auth()->user()->categories()->select('id', 'name', 'color')->orderBy('name')->get();
        
        return view('notes.index', compact('notes', 'tasks', 'categories'));
    }
    
    public function show(Note $note)
    {
        if ($note->user_id !== auth()->id()) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }
        
        $note->load(['task', 'task.categories']);
        return response()->json($note);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:90',
            'content' => 'required|max:1000',
            'task_id' => 'nullable|exists:tasks,id',
            'category_id' => 'nullable|exists:categories,id'
        ]);

        // Verificar se a tarefa pertence ao usuário
        if ($request->task_id) {
            $task = Task::find($request->task_id);
            if (!$task || $task->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tarefa inválida'
                ], 422);
            }
        }
        
        // Verificar se a categoria pertence ao usuário
        if ($request->category_id) {
            $category = Category::find($request->category_id);
            if (!$category || $category->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Categoria inválida'
                ], 422);
            }
        }

        $note = auth()->user()->notes()->create([
            'title' => $request->title,
            'content' => $request->content,
            'task_id' => $request->task_id,
            'category_id' => $request->category_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Nota criada com sucesso',
            'note' => $note
        ]);
    }

    public function update(Request $request, Note $note)
    {
        $request->validate([
            'title' => 'required|max:90',
            'content' => 'required|max:1000',
            'task_id' => 'nullable|exists:tasks,id',
            'category_id' => 'nullable|exists:categories,id'
        ]);

        if ($note->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Não autorizado'
            ], 403);
        }
        
        // Verificar se a tarefa pertence ao usuário
        if ($request->task_id) {
            $task = Task::find($request->task_id);
            if (!$task || $task->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tarefa inválida'
                ], 422);
            }
        }
        
        // Verificar se a categoria pertence ao usuário
        if ($request->category_id) {
            $category = Category::find($request->category_id);
            if (!$category || $category->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Categoria inválida'
                ], 422);
            }
        }

        $note->update([
            'title' => $request->title,
            'content' => $request->content,
            'task_id' => $request->task_id,
            'category_id' => $request->category_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Nota atualizada com sucesso',
            'note' => $note
        ]);
    }

    public function destroy(Note $note)
    {
        if ($note->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Não autorizado'
            ], 403);
        }

        $note->delete();
        return response()->json([
            'success' => true,
            'message' => 'Nota excluída com sucesso'
        ]);
    }
    
    public function details(Note $note)
    {
        if ($note->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Não autorizado'
            ], 403);
        }
        
        $note->load(['task', 'task.categories']);
        return response()->json([
            'success' => true,
            'note' => $note
        ]);
    }
    
    public function edit(Note $note)
    {
        if ($note->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Não autorizado'
            ], 403);
        }
        
        $note->load(['task', 'task.categories']);
        return response()->json([
            'success' => true,
            'note' => $note
        ]);
    }
    
    public function tasksList()
    {
        $tasks = auth()->user()->tasks()
            ->select('id', 'title')
            ->orderBy('title')
            ->get();
            
        return response()->json([
            'success' => true,
            'tasks' => $tasks
        ]);
    }
    
    public function categoriesList()
    {
        $categories = auth()->user()->categories()
            ->select('id', 'name', 'color')
            ->orderBy('name')
            ->get();
            
        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }
}

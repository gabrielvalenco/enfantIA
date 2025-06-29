<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;
use App\Models\Task;

class NoteController extends Controller
{
    public function index()
    {
        $notes = auth()->user()->notes()->with(['task', 'task.categories'])->orderBy('updated_at', 'desc')->get();
        return view('notes.index', compact('notes'));
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
            'title' => 'required|max:255',
            'content' => 'required'
            // Comentado temporariamente até resolver o problema da migração
            // 'task_id' => 'nullable|exists:tasks,id'
        ]);

        $notesCount = auth()->user()->notes()->count();
        if ($notesCount >= 3) {
            return response()->json(['error' => 'Você já atingiu o limite máximo de 3 notas.'], 422);
        }
        
        // Comentado temporariamente até resolver o problema da migração
        /*
        // Verificar se a tarefa pertence ao usuário
        if ($request->task_id) {
            $task = Task::find($request->task_id);
            if (!$task || $task->user_id !== auth()->id()) {
                return response()->json(['error' => 'Tarefa inválida'], 422);
            }
        }
        */

        $note = auth()->user()->notes()->create([
            'title' => $request->title,
            'content' => $request->content
            // Comentado temporariamente até resolver o problema da migração
            // 'task_id' => $request->task_id
        ]);

        return response()->json($note);
    }

    public function update(Request $request, Note $note)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required'
            // Comentado temporariamente até resolver o problema da migração
            // 'task_id' => 'nullable|exists:tasks,id'
        ]);

        if ($note->user_id !== auth()->id()) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }
        
        // Comentado temporariamente até resolver o problema da migração
        /*
        // Verificar se a tarefa pertence ao usuário
        if ($request->task_id) {
            $task = Task::find($request->task_id);
            if (!$task || $task->user_id !== auth()->id()) {
                return response()->json(['error' => 'Tarefa inválida'], 422);
            }
        }
        */

        $note->update([
            'title' => $request->title,
            'content' => $request->content
            // Comentado temporariamente até resolver o problema da migração
            // 'task_id' => $request->task_id
        ]);

        return response()->json($note);
    }

    public function destroy(Note $note)
    {
        if ($note->user_id !== auth()->id()) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $note->delete();
        return response()->json(['message' => 'Nota excluída com sucesso']);
    }
    
    public function details(Note $note)
    {
        if ($note->user_id !== auth()->id()) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }
        
        $note->load(['task', 'task.categories']);
        return response()->json($note);
    }
}

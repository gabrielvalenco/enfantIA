<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Subtask;
use Illuminate\Http\Request;

class SubtaskController extends Controller
{
    /**
     * Lista todas as subtarefas de uma tarefa específica
     */
    public function index(Task $task)
    {
        $subtasks = $task->subtasks()->orderBy('created_at', 'asc')->get();
        
        return response()->json([
            'success' => true,
            'subtasks' => $subtasks
        ]);
    }
    public function store(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $subtask = $task->subtasks()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Subtarefa criada com sucesso',
            'subtask' => $subtask
        ]);
    }

    public function toggleComplete(Request $request, Subtask $subtask)
    {
        $subtask->completed = !$subtask->completed;
        $subtask->save();

        return response()->json([
            'success' => true,
            'completed' => $subtask->completed,
            'all_completed' => $subtask->task->allSubtasksCompleted()
        ]);
    }

    public function destroy(Subtask $subtask)
    {
        $subtask->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Subtarefa excluída com sucesso'
        ]);
    }
}

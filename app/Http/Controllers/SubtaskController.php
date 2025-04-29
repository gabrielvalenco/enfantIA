<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Subtask;
use Illuminate\Http\Request;

class SubtaskController extends Controller
{
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

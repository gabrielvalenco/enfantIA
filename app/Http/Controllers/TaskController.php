<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = auth()->user()->tasks()->with('categories')->where('status', false)->get();
        $nearDeadlineTasks = $tasks->filter(function ($task) {
            $dueDate = Carbon::parse($task->due_date);
            $now = now();
            $hoursUntilDue = $now->diffInHours($dueDate, false);
            return $hoursUntilDue <= 24 && $hoursUntilDue > 0;
        });

        return view('tasks.index', compact('tasks', 'nearDeadlineTasks'));
    }

    public function create()
    {
        $categories = Category::where('user_id', auth()->id())->get();
        return view('tasks.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $messages = [
            'title.required' => 'O título é obrigatório.',
            'description.required' => 'A descrição é obrigatória.',
            'categories.array' => 'As categorias devem ser selecionadas corretamente.',
            'categories.max' => 'Você pode selecionar no máximo 3 categorias.',
            'categories.*.exists' => 'Uma das categorias selecionadas é inválida.',
            'due_date.required' => 'A data de vencimento é obrigatória.',
            'due_date.date' => 'A data de vencimento deve ser uma data válida.',
            'urgency.required' => 'O nível de urgência é obrigatório.',
            'urgency.in' => 'O nível de urgência selecionado é inválido.',
            'assigned_to.exists' => 'O responsável selecionado é inválido.'
        ];

        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'categories' => 'nullable|array|max:3',
            'categories.*' => 'exists:categories,id',
            'due_date' => 'required|date',
            'urgency' => 'required|in:none,low,medium,high',
            'group_id' => 'nullable|exists:groups,id',
            'assigned_to' => 'nullable|exists:users,id'
        ], $messages);

        try {
            // Converter a data e hora para o formato correto do banco
            $dueDate = Carbon::parse($request->due_date);
            
            $task = Task::create([
                'title' => $request->title,
                'description' => $request->description,
                'due_date' => $dueDate,
                'urgency' => $request->urgency,
                'status' => false,
                'user_id' => auth()->id(),
                'group_id' => $request->group_id,
                'assigned_to' => $request->assigned_to
            ]);

            if ($request->has('categories')) {
                $task->categories()->attach($request->categories);
            }
            
            // Processar subtarefas
            if ($request->has('subtasks')) {
                foreach ($request->subtasks as $subtaskData) {
                    if (!empty($subtaskData['title'])) {
                        $task->subtasks()->create([
                            'title' => $subtaskData['title'],
                            'description' => $subtaskData['description'] ?? null,
                        ]);
                    }
                }
            }

            if ($request->has('group_id')) {
                return redirect()->route('groups.show', $request->group_id)
                    ->with('success', 'Tarefa criada com sucesso no grupo!');
            }

            return redirect()->route('tasks.index')->with('success', 'Tarefa criada com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao criar a tarefa. Por favor, tente novamente.');
        }
    }

    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        $categories = Category::where('user_id', auth()->id())->get();
        
        // Retornar JSON se for uma requisição AJAX
        if (request()->ajax()) {
            return response()->json([
                'task' => $task,
                'categories' => $categories,
                'task_categories' => $task->categories->pluck('id')->toArray()
            ]);
        }
        
        // Caso contrário, retornar a view normal
        $taskCategories = $task->categories->pluck('id')->toArray();
        return view('tasks.edit', compact('task', 'categories', 'taskCategories'));
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);
        
        $messages = [
            'title.required' => 'O título é obrigatório.',
            'description.required' => 'A descrição é obrigatória.',
            'categories.array' => 'As categorias devem ser selecionadas corretamente.',
            'categories.max' => 'Você pode selecionar no máximo 3 categorias.',
            'categories.*.exists' => 'Uma das categorias selecionadas é inválida.',
            'due_date.required' => 'A data de vencimento é obrigatória.',
            'due_date.date' => 'A data de vencimento deve ser uma data válida.',
            'urgency.required' => 'O nível de urgência é obrigatório.',
            'urgency.in' => 'O nível de urgência selecionado é inválido.'
        ];

        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'categories' => 'nullable|array|max:3',
            'categories.*' => 'exists:categories,id',
            'due_date' => 'required|date',
            'urgency' => 'required|in:none,low,medium,high'
        ], $messages);

        try {
            // Converter a data e hora para o formato correto do banco
            $dueDate = Carbon::parse($request->due_date);
            
            $task->update([
                'title' => $request->title,
                'description' => $request->description,
                'due_date' => $dueDate,
                'urgency' => $request->urgency
            ]);

            // Sincronizar categorias mesmo se não houver nenhuma selecionada
            $task->categories()->sync($request->input('categories', []));

            return redirect()->route('tasks.index')->with('success', 'Tarefa atualizada com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao atualizar a tarefa. Por favor, tente novamente.');
        }
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        try {
            $task->delete();
            return redirect()->route('tasks.index')->with('success', 'Tarefa excluída com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao excluir a tarefa. Por favor, tente novamente.');
        }
    }

    public function complete(Task $task)
    {
        // Verificar se todas as subtarefas estão concluídas
        if (!$task->allSubtasksCompleted()) {
            return redirect()->back()->with('error', 'Esta tarefa possui subtarefas pendentes!');
        }
        
        $task->update([
            'status' => true,
            'completed_at' => now()
        ]);

        if (url()->previous() === route('dashboard')) {
            return redirect()->route('tasks.completed')->with('success', 'Tarefa marcada como concluída!');
        }

        return redirect()->back()->with('success', 'Tarefa marcada como concluída!');
    }

    public function completed()
    {
        $tasks = auth()->user()->tasks()->with('categories')->where('status', true)->get();
        return view('tasks.completed', compact('tasks'));
    }

    public function uncomplete(Task $task)
    {
        $this->authorize('update', $task);
        try {
            $task->update(['status' => false]);
            return redirect()->route('tasks.completed')->with('success', 'Tarefa marcada como não concluída!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao desfazer conclusão da tarefa. Por favor, tente novamente.');
        }
    }
    
    public function details(Task $task)
    {
        $this->authorize('view', $task);
        $task->load('subtasks');
        
        return response()->json([
            'task' => $task,
            'due_date_formatted' => $task->due_date->format('d/m/Y H:i'),
            'urgency_formatted' => ucfirst($task->urgency),
            'subtasks' => $task->subtasks
        ]);
    }

    public function canComplete(Task $task)
    {
        $this->authorize('update', $task);
        
        return response()->json([
            'can_complete' => $task->allSubtasksCompleted()
        ]);
    }

    /**
     * Remove all selected completed tasks.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function clearSelected(Request $request)
    {
        if (!$request->has('selected_tasks') || !is_array($request->selected_tasks)) {
            return redirect()->route('tasks.completed')->with('error', 'Nenhuma tarefa selecionada para excluir.');
        }

        try {
            // Filtra apenas as tarefas concluídas e que pertencem ao usuário atual
            $tasks = Task::where('status', true)
                ->where('user_id', auth()->id())
                ->whereIn('id', $request->selected_tasks)
                ->get();

            foreach ($tasks as $task) {
                $task->delete();
            }

            $count = count($tasks);
            $message = $count > 1 
                ? "{$count} tarefas excluídas com sucesso!" 
                : "Tarefa excluída com sucesso!";

            return redirect()->route('tasks.completed')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('tasks.completed')->with('error', 'Erro ao excluir tarefas. Por favor, tente novamente.');
        }
    }

    /**
     * Remove all completed tasks of the authenticated user.
     *
     * @return \Illuminate\Http\Response
     */
    public function clearAll()
    {
        try {
            $count = Task::where('status', true)
                ->where('user_id', auth()->id())
                ->count();

            Task::where('status', true)
                ->where('user_id', auth()->id())
                ->delete();

            $message = $count > 1 
                ? "Todas as {$count} tarefas concluídas foram excluídas com sucesso!" 
                : "Todas as tarefas concluídas foram excluídas com sucesso!";

            return redirect()->route('tasks.completed')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('tasks.completed')->with('error', 'Erro ao excluir tarefas. Por favor, tente novamente.');
        }
    }
    
    /**
     * Complete multiple tasks at once.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function completeMultiple(Request $request)
    {
        if (!$request->has('task_ids') || !is_array($request->task_ids)) {
            return redirect()->route('tasks.index')->with('error', 'Nenhuma tarefa selecionada para concluir.');
        }

        try {
            // Filtra apenas as tarefas pendentes que pertencem ao usuário atual
            $tasks = Task::where('status', false)
                ->where('user_id', auth()->id())
                ->whereIn('id', $request->task_ids)
                ->get();

            $completedCount = 0;
            $incompleteSubtasks = 0;

            foreach ($tasks as $task) {
                // Verificar se todas as subtarefas estão concluídas
                if ($task->allSubtasksCompleted()) {
                    $task->update([
                        'status' => true,
                        'completed_at' => now()
                    ]);
                    $completedCount++;
                } else {
                    $incompleteSubtasks++;
                }
            }

            // Preparar mensagem de sucesso/aviso
            if ($completedCount > 0 && $incompleteSubtasks > 0) {
                $message = "{$completedCount} tarefa(s) marcada(s) como concluída(s)! {$incompleteSubtasks} tarefa(s) não puderam ser concluídas por terem subtarefas pendentes.";
                $type = 'warning';
            } elseif ($completedCount > 0) {
                $message = $completedCount > 1 
                    ? "{$completedCount} tarefas marcadas como concluídas com sucesso!" 
                    : "Tarefa marcada como concluída com sucesso!";
                $type = 'success';
            } else {
                $message = "Nenhuma tarefa pôde ser concluída. Verifique se há subtarefas pendentes.";
                $type = 'error';
            }

            return redirect()->route('tasks.index')->with($type, $message);
        } catch (\Exception $e) {
            return redirect()->route('tasks.index')->with('error', 'Erro ao concluir tarefas. Por favor, tente novamente.');
        }
    }

    /**
     * Delete multiple tasks at once.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteMultiple(Request $request)
    {
        if (!$request->has('task_ids') || !is_array($request->task_ids)) {
            return redirect()->route('tasks.index')->with('error', 'Nenhuma tarefa selecionada para excluir.');
        }

        try {
            // Filtra apenas as tarefas que pertencem ao usuário atual
            $tasks = Task::where('user_id', auth()->id())
                ->whereIn('id', $request->task_ids)
                ->get();

            foreach ($tasks as $task) {
                $task->delete();
            }

            $count = count($tasks);
            $message = $count > 1 
                ? "{$count} tarefas excluídas com sucesso!" 
                : "Tarefa excluída com sucesso!";

            return redirect()->route('tasks.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('tasks.index')->with('error', 'Erro ao excluir tarefas. Por favor, tente novamente.');
        }
    }
}

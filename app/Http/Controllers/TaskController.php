<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Category;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::where('status', false)
                     ->with('categories')
                     ->orderBy('due_date', 'asc')
                     ->get();

        $nearDeadlineTasks = $tasks->filter(function ($task) {
            $dueDate = \Carbon\Carbon::parse($task->due_date);
            $now = now();
            $hoursUntilDue = $now->diffInHours($dueDate, false);
            return $hoursUntilDue <= 24 && $hoursUntilDue > 0;
        });

        return view('tasks.index', compact('tasks', 'nearDeadlineTasks'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('tasks.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $messages = [
            'title.required' => 'O título é obrigatório.',
            'description.required' => 'A descrição é obrigatória.',
            'categories.required' => 'Selecione pelo menos uma categoria.',
            'categories.array' => 'As categorias devem ser selecionadas corretamente.',
            'categories.min' => 'Selecione pelo menos uma categoria.',
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
            'categories' => 'required|array|min:1|max:3',
            'categories.*' => 'exists:categories,id',
            'due_date' => 'required|date',
            'urgency' => 'required|in:none,low,medium,high'
        ], $messages);

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'urgency' => $request->urgency,
            'status' => false
        ]);

        $task->categories()->attach($request->categories);

        return redirect()->route('tasks.index')
            ->with('success', 'Tarefa criada com sucesso!');
    }

    public function edit(Task $task)
    {
        $categories = Category::all();
        return view('tasks.edit', compact('task', 'categories'));
    }

    public function update(Request $request, Task $task)
    {
        $messages = [
            'title.required' => 'O título é obrigatório.',
            'description.required' => 'A descrição é obrigatória.',
            'categories.required' => 'Selecione pelo menos uma categoria.',
            'categories.array' => 'As categorias devem ser selecionadas corretamente.',
            'categories.min' => 'Selecione pelo menos uma categoria.',
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
            'categories' => 'required|array|min:1|max:3',
            'categories.*' => 'exists:categories,id',
            'due_date' => 'required|date',
            'urgency' => 'required|in:none,low,medium,high'
        ], $messages);

        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'urgency' => $request->urgency
        ]);

        $task->categories()->sync($request->categories);

        return redirect()->route('tasks.index')
            ->with('success', 'Tarefa atualizada com sucesso!');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index')
            ->with('success', 'Tarefa excluída com sucesso!');
    }

    public function complete(Task $task)
    {
        $task->update(['status' => !$task->status]);
        return redirect()->route('tasks.index')
            ->with('success', $task->status ? 'Tarefa marcada como concluída!' : 'Tarefa marcada como pendente!');
    }

    public function completed()
    {
        $tasks = Task::where('status', true)
                     ->with('categories')
                     ->orderBy('updated_at', 'desc')
                     ->get();

        return view('tasks.completed', compact('tasks'));
    }
}

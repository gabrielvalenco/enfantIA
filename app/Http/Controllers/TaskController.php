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
        $tasks = Task::where('status', false)
                     ->with('categories')
                     ->orderBy('due_date', 'asc')
                     ->get();

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
        $categories = Category::all();
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
            
            $task = Task::create([
                'title' => $request->title,
                'description' => $request->description,
                'due_date' => $dueDate,
                'urgency' => $request->urgency
            ]);

            if ($request->has('categories')) {
                $task->categories()->attach($request->categories);
            }

            return redirect()->route('tasks.index')->with('success', 'Tarefa criada com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao criar a tarefa. Por favor, tente novamente.');
        }
    }

    public function edit(Task $task)
    {
        $categories = Category::all();
        $taskCategories = $task->categories->pluck('id')->toArray();
        return view('tasks.edit', compact('task', 'categories', 'taskCategories'));
    }

    public function update(Request $request, Task $task)
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
        try {
            $task->delete();
            return redirect()->route('tasks.index')->with('success', 'Tarefa excluída com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao excluir a tarefa. Por favor, tente novamente.');
        }
    }

    public function complete(Task $task)
    {
        try {
            $task->update(['status' => true]);
            return redirect()->back()->with('success', 'Tarefa marcada como concluída!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao concluir a tarefa. Por favor, tente novamente.');
        }
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

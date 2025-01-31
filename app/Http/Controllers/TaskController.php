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
                'urgency' => $request->urgency,
                'status' => false,
                'user_id' => auth()->id()
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
        $this->authorize('update', $task);
        $categories = Category::where('user_id', auth()->id())->get();
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
        $this->authorize('update', $task);
        try {
            $task->update(['status' => true]);
            return redirect()->route('tasks.index')->with('success', 'Tarefa marcada como concluída!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao concluir a tarefa. Por favor, tente novamente.');
        }
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
}

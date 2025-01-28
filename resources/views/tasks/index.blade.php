<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Tarefas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/custom-styles.css') }}">
</head>
<body class="bg-light">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($nearDeadlineTasks->isNotEmpty())
            <div class="alert alert-warning">
                <h5 class="alert-heading mb-2">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Atenção: Tarefas Próximas do Prazo
                </h5>
                <ul class="mb-0">
                    @foreach($nearDeadlineTasks as $task)
                        <li>
                            "{{ $task->title }}" - 
                            Vence em: {{ \Carbon\Carbon::parse($task->due_date)->diffForHumans() }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="table-header">
            <h1 class="table-title">Lista de Tarefas</h1>
            <div class="table-actions">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Voltar</a>
                <a href="{{ route('tasks.create') }}" class="btn btn-primary">Nova Tarefa</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Descrição</th>
                        <th>Categorias</th>
                        <th>Urgência</th>
                        <th>Status</th>
                        <th class="due-date-header">Data de Vencimento</th>
                        <th class="actions-column">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @if($tasks->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center">
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Não há tarefas pendentes no momento.
                                </div>
                            </td>
                        </tr>
                    @else
                        @foreach($tasks as $task)
                            @if(!$task->status)
                                <tr @if($nearDeadlineTasks->contains($task)) class="table-warning" @endif>
                                    <td class="task-title">{{ $task->title }}</td>
                                    <td class="task-description">{{ $task->description }}</td>
                                    <td class="categories-column">
                                        @foreach($task->categories as $category)
                                            <span class="badge badge-info">{{ $category->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <span class="badge {{ $task->urgency == 'high' ? 'badge-danger' : ($task->urgency == 'medium' ? 'badge-warning' : 'badge-success') }}">
                                            {{ $task->urgency == 'high' ? 'Alta' : ($task->urgency == 'medium' ? 'Média' : ($task->urgency == 'low' ? 'Baixa' : 'Nenhuma')) }}
                                        </span>
                                    </td>
                                    <td>
                                        <form action="{{ route('tasks.complete', $task) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm {{ $task->status ? 'btn-success' : 'btn-secondary' }}">
                                                {{ $task->status ? 'Concluída' : 'Pendente' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="due-date">
                                        {{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y H:i') }}
                                        @if($nearDeadlineTasks->contains($task))
                                            <span class="badge badge-warning">
                                                <i class="fas fa-clock mr-1"></i>
                                                Prazo próximo
                                            </span>
                                        @endif
                                    </td>
                                    <td class="actions-column">
                                        <div class="action-buttons">
                                            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-warning action-btn">
                                                <span class="action-text">Editar</span>
                                                <i class="fas fa-edit action-icon"></i>
                                            </a>
                                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger action-btn" onclick="return confirm('Tem certeza que deseja excluir esta tarefa?')">
                                                    <span class="action-text">Excluir</span>
                                                    <i class="fas fa-trash action-icon"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

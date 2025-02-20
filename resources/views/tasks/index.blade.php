<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Tarefas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/custom-styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/task-form.css') }}">
    <link rel="stylesheet" href="{{ asset('css/category-style.css') }}">
</head>
<body class="bg-light">
    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @foreach($tasks as $task)
            @php
                $dueDate = \Carbon\Carbon::parse($task->due_date);
                $now = \Carbon\Carbon::now();
                $hoursUntilDue = $now->diffInHours($dueDate, false);
            @endphp

            @if($hoursUntilDue <= 24 && $hoursUntilDue > 0)
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <div>
                            <strong>Atenção!</strong> A tarefa "<span class="fw-bold">{{ $task->title }}</span>" vence em menos de 24 horas.
                            <br>
                            <small>Data de vencimento: {{ $dueDate->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

        <div class="table-header">
            <h1>Lista de Tarefas</h1>
            <div class="table-actions">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Voltar</a>
                <a href="{{ route('tasks.create') }}" class="btn btn-primary">Nova Tarefa</a>
            </div>
        </div>

            <table class="table table-striped">
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
                                <tr>
                                    <td class="task-title">{{ $task->title }}</td>
                                    <td class="task-description">{{ $task->description }}</td>
                                    <td class="categories-column">
                                        @if($task->categories->isNotEmpty())
                                            <div class="d-block gap-2">
                                                @foreach($task->categories as $category)
                                                    <div class="category-badge p-2 m-1" style="background-color: {{ $category->color }};">
                                                        {{ $category->name }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted">Sem categoria</span>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($task->urgency)
                                            @case('high')
                                                @php
                                                    $dueDate = \Carbon\Carbon::parse($task->due_date);
                                                    $now = \Carbon\Carbon::now();
                                                    $hoursUntilDue = $now->diffInHours($dueDate, false);
                                                @endphp
                                                @if($hoursUntilDue <= 24 && $hoursUntilDue > 0)
                                                    <span class="badge-urgent p-2">
                                                        <i class="fas fa-exclamation-circle"></i>
                                                        Alta Prioridade
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger p-2">Alta</span>
                                                @endif
                                                @break
                                            @case('medium')
                                                <span class="badge bg-warning text-dark p-2">Média</span>
                                                @break
                                            @default
                                                <span class="badge bg-info p-2">Baixa</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <form action="{{ route('tasks.complete', $task) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm pb-1 {{ $task->status ? 'btn-secondary' : 'btn-secondary' }}">
                                                <i class="fas fa-clock"></i> {{ $task->status ? 'Concluída' : ''}}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="due-date">
                                        {{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y H:i') }}
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

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cria o elemento de áudio
        const audio = new Audio("{{ asset('notification.mp3') }}");
        
        // Verifica se há tarefas urgentes
        const urgentTasks = document.querySelectorAll('.badge-urgent');
        if (urgentTasks.length > 0) {
            // Toca o som de notificação
            audio.play().catch(function(error) {
                console.log("Reprodução de áudio não permitida");
            });
        }
    });
    </script>
    @endpush
</body>
</html>

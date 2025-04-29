<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Tarefas</title>

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/task/index.css') }}">
</head>
<body>
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
                <a class="back-button" href="{{ route('dashboard') }}">
                    Voltar ao Dashboard
                </a>
                <a class="add-task-button" href="{{ route('tasks.create') }}">
                    <i class="fas fa-plus-circle"></i> Nova Tarefa
                </a>
            </div>
        </div>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Descrição</th>
                        <th class="text-center">Categorias</th>
                        <th>Urgência</th>
                        <th class="text-center">Status</th>
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
                                <tr class="task-row" data-task-id="{{ $task->id }}">
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
                                                <span class="badge bg-warning p-2">Média</span>
                                                @break
                                            @default
                                                <span class="badge bg-info p-2">Baixa</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <form action="{{ route('tasks.complete', $task) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn index-status btn-sm pb-1 {{ $task->status ? 'btn-secondary' : 'btn-secondary' }}">
                                                <i class="fas fa-clock"></i> {{ $task->status ? 'Concluída' : ''}}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="due-date">
                                        {{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="voltar">
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

    <!-- Modal para Subtarefas -->
    <div class="modal fade" id="subtasksModal" tabindex="-1" aria-labelledby="subtasksModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="subtasksModalLabel">Detalhes da Tarefa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="task-info">
                        <h4 id="task-title"></h4>
                        <p id="task-description"></p>
                        <div class="d-flex flex-column">
                            <div class="mb-3">
                                <strong>Data de Vencimento:</strong> <span id="task-due-date"></span>
                            </div>
                            <div>
                                <strong>Urgência:</strong> <span id="task-urgency"></span>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h5>Subtarefas</h5>
                    <div id="subtasks-list" class="mb-3">
                        <!-- Lista de subtarefas será carregada via AJAX -->
                        <div class="text-center py-3 d-none" id="no-subtasks">
                            <p class="text-muted">Esta tarefa não possui subtarefas.</p>
                        </div>
                    </div>
                    
                    <div id="add-subtask-form">
                        <form id="subtask-form">
                            <div class="form-group">
                                <input type="text" class="form-control" id="subtask-title" placeholder="Nova subtarefa..." required>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary mt-2">Adicionar Subtarefa</button>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" id="complete-task-btn">
                        Concluir Tarefa
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            let currentTaskId = null;
            
            // Abrir modal ao clicar em uma tarefa
            $('.task-row').on('click', function() {
                const taskId = $(this).data('task-id');
                currentTaskId = taskId;
                
                console.log('Clicou na tarefa ID:', taskId); // Debug

                // Buscar informações da tarefa e subtarefas
                $.ajax({
                    url: `/tasks/${taskId}/details`,
                    method: 'GET',
                    success: function(response) {
                        console.log('Resposta recebida:', response); // Debug
                        
                        // Preencher informações da tarefa
                        $('#task-title').text(response.task.title);
                        $('#task-description').text(response.task.description);
                        $('#task-due-date').text(response.due_date_formatted);
                        $('#task-urgency').text(response.urgency_formatted);
                        
                        // Preencher subtarefas
                        const subtasksList = $('#subtasks-list');
                        subtasksList.empty();
                        
                        if (response.subtasks.length === 0) {
                            $('#no-subtasks').removeClass('d-none');
                        } else {
                            $('#no-subtasks').addClass('d-none');
                            
                            response.subtasks.forEach(function(subtask) {
                                subtasksList.append(`
                                    <div class="subtask-item d-flex align-items-center mb-2" data-id="${subtask.id}">
                                        <div class="form-check">
                                            <input class="form-check-input subtask-checkbox" 
                                                   type="checkbox" 
                                                   ${subtask.completed ? 'checked' : ''}
                                                   id="subtask-${subtask.id}">
                                            <label class="form-check-label ${subtask.completed ? 'text-muted text-decoration-line-through' : ''}" 
                                                   for="subtask-${subtask.id}">
                                                ${subtask.title}
                                            </label>
                                        </div>
                                        <button class="btn btn-sm btn-danger delete-subtask ml-auto">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                `);
                            });
                        }
                        
                        // Mostrar o modal
                        $('#subtasksModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error('Erro ao carregar dados:', error); // Debug
                        alert('Erro ao carregar os detalhes da tarefa');
                    }
                });
            });
            
            // Adicionar nova subtarefa
            $('#subtask-form').on('submit', function(e) {
                e.preventDefault();
                
                const title = $('#subtask-title').val();
                if (!title) return;
                
                $.ajax({
                    url: `/tasks/${currentTaskId}/subtasks`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        title: title
                    },
                    success: function(response) {
                        const subtasksList = $('#subtasks-list');
                        $('#no-subtasks').addClass('d-none');
                        
                        subtasksList.append(`
                            <div class="subtask-item d-flex align-items-center mb-2" data-id="${response.subtask.id}">
                                <div class="form-check">
                                    <input class="form-check-input subtask-checkbox" 
                                           type="checkbox" 
                                           id="subtask-${response.subtask.id}">
                                    <label class="form-check-label" for="subtask-${response.subtask.id}">
                                        ${response.subtask.title}
                                    </label>
                                </div>
                                <button class="btn btn-sm btn-danger delete-subtask ml-auto">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `);
                        
                        $('#subtask-title').val('');
                    }
                });
            });
            
            // Concluir/desmarcar subtarefa
            $(document).on('change', '.subtask-checkbox', function() {
                const subtaskId = $(this).closest('.subtask-item').data('id');
                const checkbox = $(this);
                const label = checkbox.siblings('label');
                
                $.ajax({
                    url: `/subtasks/${subtaskId}/toggle`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.completed) {
                            label.addClass('text-muted text-decoration-line-through');
                        } else {
                            label.removeClass('text-muted text-decoration-line-through');
                        }
                    }
                });
            });
            
            // Excluir subtarefa
            $(document).on('click', '.delete-subtask', function(e) {
                e.stopPropagation();
                
                const subtaskItem = $(this).closest('.subtask-item');
                const subtaskId = subtaskItem.data('id');
                
                Swal.fire({
                    title: 'Tem certeza?',
                    text: 'Você deseja excluir esta subtarefa?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, excluir',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/subtasks/${subtaskId}`,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function() {
                                subtaskItem.remove();
                                
                                // Mostrar mensagem se não houver mais subtarefas
                                if ($('#subtasks-list .subtask-item').length === 0) {
                                    $('#no-subtasks').removeClass('d-none');
                                }
                                
                                Swal.fire(
                                    'Excluída!',
                                    'A subtarefa foi excluída com sucesso.',
                                    'success'
                                );
                            }
                        });
                    }
                });
            });
            
            // Concluir tarefa
            $('#complete-task-btn').on('click', function() {
                $.ajax({
                    url: `/tasks/${currentTaskId}/can-complete`,
                    method: 'GET',
                    success: function(response) {
                        if (response.can_complete) {
                            // Enviar formulário de conclusão
                            window.location.href = `/tasks/${currentTaskId}/complete`;
                        } else {
                            Swal.fire({
                                title: 'Não é possível concluir',
                                text: 'Esta tarefa possui subtarefas pendentes!',
                                icon: 'warning',
                                confirmButtonText: 'Ok'
                            });
                        }
                    }
                });
            });
        });
    </script>

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

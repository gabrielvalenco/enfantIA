@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/dashboard-style.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/pt-br.js"></script>

<header class="dashboard-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="dashboard-title">TASKNEST</h1>
        
        <div class="dropdown">
            <button class="btn btn-link text-light p-0 border-0" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user-circle fs-4"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end user-dropdown" aria-labelledby="userDropdown">
                <li>
                    <div class="dropdown-header">
                        <span class="fw-bold">{{ Auth::user()->name }}</span>
                        <br>
                        <small class="text-muted">{{ Auth::user()->email }}</small>
                    </div>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a href="{{ route('profile.edit') }}" class="dropdown-item">
                        <i class="fas fa-user-edit me-2"></i>
                        Editar Perfil
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                    <li>
                        <a href="{{ route('groups.index') }}" class="dropdown-item">
                        <i class="fas fa-users me-2"></i>
                            Grupo
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                <li>
                    <a href="{{ route('notes.index') }}" class="dropdown-item">
                        <i class="fas fa-sticky-note me-2"></i>
                        Bloco de notas
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" class="px-2 py-1">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>
                            Sair
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>

<div class="container mt-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Status Cards -->
    <div class="status-cards">
        <div class="status-card">
            <div class="status-card-icon pending">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="status-card-info">
                <h3>{{ $pendingTasks ?? 0 }}</h3>
                <p>Tarefas Pendentes</p>
            </div>
        </div>
        
        <div class="status-card">
            <div class="status-card-icon completed">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="status-card-info">
                <h3>{{ $completedTasks ?? 0 }}</h3>
                <p>Tarefas Concluídas</p>
            </div>
        </div>
        
        <div class="status-card">
            <div class="status-card-icon urgent">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="status-card-info">
                <h3>{{ $urgentTasks ?? 0 }}</h3>
                <p>Tarefas Urgentes</p>
            </div>
        </div>
        
        <div class="status-card">
            <div class="status-card-icon categories">
                <i class="fas fa-tags"></i>
            </div>
            <div class="status-card-info">
                <h3>{{ $categories ?? 0 }}</h3>
                <p>Categorias</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="dashboard-menu">
        <div class="dashboard-section-title">
            <i class="fas fa-bolt"></i>
            Ações Rápidas
        </div>
        
        <div class="menu-grid">
            <a href="{{ route('tasks.create') }}" class="menu-item">
                <div class="menu-item-icon">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <div class="menu-item-content">
                    <h3>Criar Tarefa</h3>
                    <p>Adicione uma nova tarefa ao sistema</p>
                </div>
            </a>

            <a href="{{ route('tasks.index') }}" class="menu-item">
                <div class="menu-item-icon">
                    <i class="fas fa-list-ul"></i>
                </div>
                <div class="menu-item-content">
                    <h3>Tarefas Pendentes</h3>
                    <p>Visualize e gerencie suas tarefas ativas</p>
                </div>
            </a>

            <a href="{{ route('tasks.completed') }}" class="menu-item">
                <div class="menu-item-icon">
                    <i class="fas fa-check-double"></i>
                </div>
                <div class="menu-item-content">
                    <h3>Tarefas Concluídas</h3>
                    <p>Veja o histórico de tarefas finalizadas</p>
                </div>
            </a>

            <a href="{{ route('categories.index') }}" class="menu-item">
                <div class="menu-item-icon">
                    <i class="fas fa-tags"></i>
                </div>
                <div class="menu-item-content">
                    <h3>Categorias</h3>
                    <p>Gerencie as categorias de tarefas</p>
                </div>
            </a>

            <a href="{{ route('notes.index') }}" class="menu-item">
                <div class="menu-item-icon">
                    <i class="fas fa-sticky-note"></i>
                </div>
                <div class="menu-item-content">
                    <h3>Bloco de Notas</h3>
                    <p>Gerencie suas anotações pessoais</p>
                </div>
            </a>
        </div>
    </div>

    <div class="upcoming-tasks mt-4">
        <div class="dashboard-section-title">
            <i class="fas fa-clock"></i>
            Prazos Próximos
        </div>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Tarefa</th>
                    <th>Prazo</th>
                    <th>Urgência</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($upcomingTasks as $task)
                <tr>
                    <td>{{ $task->title }}</td>
                    <td>{{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge bg-{{ $task->urgency === 'high' ? 'danger' : ($task->urgency === 'medium' ? 'warning' : 'info') }}">
                            {{ ucfirst($task->urgency) }}
                        </span>
                    </td>
                    <td>
                        <button onclick="confirmComplete({{ $task->id }}, '{{ $task->title }}')" class="btn btn-sm btn-success" title="Concluir">
                            <i class="fas fa-check"></i>
                        </button>
                        <form id="complete-form-{{ $task->id }}" action="{{ route('tasks.complete', $task->id) }}" method="POST" class="d-none">
                            @csrf
                            @method('PATCH')
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="calendar mt-4">
        <div class="dashboard-section-title">
            <i class="fas fa-calendar-alt"></i>
            Calendário de Tarefas
        </div>
        <div id="calendar"></div>
    </div>

    <!-- Modal da Tarefa -->
    <div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="taskModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="task-info mb-3">
                        <p class="mb-1"><strong>Categoria:</strong> <span id="taskCategory"></span></p>
                        <p class="mb-1"><strong>Data:</strong> <span id="taskDate"></span></p>
                        <p class="mb-1"><strong>Urgência:</strong> <span id="taskUrgency"></span></p>
                        <p class="mb-1"><strong>Descrição:</strong></p>
                        <p id="taskDescription" class="text-muted mb-3"></p>
                    </div>
                    <div class="d-flex justify-content-center">
                        <a href="#" id="editTaskBtn" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button id="completeTaskBtn" class="btn btn-sm btn-success">
                            <i class="fas fa-check"></i>
                        </button>
                        <button id="deleteTaskBtn" class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'pt-br',
                events: [
                    @foreach ($upcomingTasks as $task)
                    {
                        id: {{ $task->id }},
                        title: '{{ $task->category ? $task->category->name . " • " : "" }}{{ $task->title }}',
                        start: '{{ $task->due_date }}',
                        color: '{{ $task->urgency === 'high' ? '#dc3545' : ($task->urgency === 'medium' ? '#ffc107' : '#0d6efd') }}',
                        display: 'block',
                        textColor: 'var(--primary)',
                        extendedProps: {
                            category: '{{ $task->category ? $task->category->name : "Sem categoria" }}',
                            description: '{{ $task->description ?? "Sem descrição" }}',
                            urgency: '{{ $task->urgency }}',
                            editUrl: '{{ route('tasks.edit', $task->id) }}',
                            deleteUrl: '{{ route('tasks.destroy', $task->id) }}',
                            completeUrl: '{{ route('tasks.complete', $task->id) }}'
                        }
                    },
                    @endforeach
                ],
                eventDidMount: function(info) {
                    info.el.style.borderLeft = '8px solid ' + info.event.backgroundColor;
                    info.el.style.backgroundColor = 'rgba(' + 
                        hexToRgb(info.event.backgroundColor).r + ',' +
                        hexToRgb(info.event.backgroundColor).g + ',' +
                        hexToRgb(info.event.backgroundColor).b + ', 0.1)';
                    
                    // Adicionar título completo como tooltip
                    info.el.title = info.event.title;
                },
                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    const event = info.event;
                    const props = event.extendedProps;
                    
                    // Preencher o modal com as informações da tarefa
                    document.getElementById('taskModalLabel').textContent = event.title;
                    document.getElementById('taskCategory').textContent = props.category;
                    document.getElementById('taskDate').textContent = new Date(event.start).toLocaleDateString('pt-BR');
                    document.getElementById('taskDescription').textContent = props.description;
                    document.getElementById('taskUrgency').textContent = props.urgency.charAt(0).toUpperCase() + props.urgency.slice(1);
                    
                    // Configurar URLs dos botões
                    document.getElementById('editTaskBtn').href = props.editUrl;
                    document.getElementById('deleteTaskBtn').onclick = () => deleteTask(event.id, props.deleteUrl);
                    document.getElementById('completeTaskBtn').onclick = () => completeTask(event.id, props.completeUrl);
                    
                    // Abrir o modal
                    new bootstrap.Modal(document.getElementById('taskModal')).show();
                }
            });
            calendar.render();
        });

        function deleteTask(taskId, deleteUrl) {
            if (confirm('Tem certeza que deseja excluir esta tarefa?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = deleteUrl;
                form.style.display = 'none';

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';

                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = document.querySelector('meta[name="csrf-token"]').content;

                form.appendChild(methodInput);
                form.appendChild(tokenInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function completeTask(taskId, completeUrl) {
            if (confirm('Deseja marcar esta tarefa como concluída?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = completeUrl;
                form.style.display = 'none';

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PATCH';

                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = document.querySelector('meta[name="csrf-token"]').content;

                form.appendChild(methodInput);
                form.appendChild(tokenInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Função auxiliar para converter hex para RGB
        function hexToRgb(hex) {
            var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            return result ? {
                r: parseInt(result[1], 16),
                g: parseInt(result[2], 16),
                b: parseInt(result[3], 16)
            } : null;
        }
    </script>

    <script>
        function confirmComplete(taskId, taskTitle) {
            if (confirm(`Deseja marcar a tarefa "${taskTitle}" como concluída?`)) {
                document.getElementById(`complete-form-${taskId}`).submit();
            }
        }
    </script>
</div>
@endsection

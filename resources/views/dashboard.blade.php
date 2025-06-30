@extends('layouts.app')

@section('content')

<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<link rel="stylesheet" href="{{ asset('css/dashboard-style.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/pt-br.js"></script>

<header class="dashboard-header">
    <div class="d-flex justify-content-between align-items-center">
        <!-- <img src="{{ asset('favicon.svg') }}" alt="Task Nest Logo" class="logo"> -->
        <h1 class="dashboard-title">{{ env('APP_NAME') }}</h1>
        
        <div class="dropdown">
            <button class="btn btn-link text-light p-0 border-0  mb-0" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
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
                    <a href="{{ route('profile.index') }}" class="dropdown-item">
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
                        <button type="submit" class="logout-btn">
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

    @if(isset($pendingInvitations) && $pendingInvitations->count() > 0)
    <!-- Notifications for Group Invitations -->
    <div class="notification-box mt-4 mb-4">
        <div class="dashboard-section-group-title">
            <i class="fas fa-bell"></i>
            Notificações <span class="badge bg-danger">{{ $pendingInvitations->count() }}</span>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="list-group">
                    @foreach($pendingInvitations as $invitation)
                        <div class="list-group-item list-group-item-action mb-2 border rounded">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-1">Convite para o grupo: {{ $invitation->group->name }}</h5>
                                    <p class="mb-1">
                                        <small class="text-muted">
                                            <i class="fas fa-user"></i> Enviado por: {{ $invitation->group->creator->name }}
                                        </small>
                                    </p>
                                    <p class="mb-1">{{ $invitation->group->description }}</p>
                                    <p class="mb-0">
                                        <small class="text-muted">
                                            <i class="fas fa-clock"></i> Recebido em: {{ $invitation->created_at->format('d/m/Y H:i') }}
                                        </small>
                                    </p>
                                </div>
                                <div class="d-flex">
                                    <form action="{{ route('invitations.accept', $invitation) }}" method="POST" class="me-2">
                                        @csrf
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check"></i> Aceitar
                                        </button>
                                    </form>
                                    <form action="{{ route('invitations.reject', $invitation) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-times"></i> Rejeitar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="text-end mt-2">
                    <a href="{{ route('invitations.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-envelope"></i> Ver todos os convites
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

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

            <a href="{{ route('reports.index') }}" class="menu-item">
                <div class="menu-item-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="menu-item-content">
                    <h3>Relatório de Desempenho</h3>
                    <p>Melhore sua produtividade com a ajuda do BIRDU</p>
                </div>
            </a>
            
        </div>
    </div>

    <div class="upcoming-tasks mt-4">
        <div class="dashboard-section-title">
            <i class="fas fa-clock"></i> Prazos Próximos
        </div>

            <div class="table-responsive">
                <table class="table table-hover custom-table">
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
                            <td class="text-center">
                                <button onclick="confirmComplete({{ $task->id }}, '{{ $task->title }}')" class="complete-button" title="Concluir">
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
        </div>

        <div id="calendar"></div>

    <!-- Modal da Tarefa -->
    <div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="taskModalLabel"></h5>
                    <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="task-info mb-3">
                        <div class="calendar-task-desc">
                            <p class="mb-1"><strong>Categoria:</strong> <span id="taskCategory"></span></p>
                            <p class="mb-1"><strong>Data:</strong> <span id="taskDate"></span></p>
                            <p class="mb-1"><strong>Urgência:</strong> <span id="taskUrgency"></span></p>
                        </div>
                        <p id="taskDescription" class="text-muted mb-3"></p>
                        
                        <!-- Subtarefas -->
                        <div id="subtasksContainer" class="subtasks-section mt-4">
                            <h6 class="subtasks-title">Subtarefas</h6>
                            <div id="subtasksList" class="subtasks-list">
                                <!-- As subtarefas serão inseridas aqui via JavaScript -->
                            </div>
                            <p id="noSubtasks" class="noSubtasks d-none">Nenhuma subtarefa cadastrada.</p>
                        </div>
                    </div>
                    <div class="modal-actions d-flex justify-content-center gap-1">
                        <a href="{{ route('tasks.index', ['open_task' => ':taskId']) }}" class="view-task-btn" data-task-id="">
                            <i class="fas fa-eye"></i> Visualizar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Define category colors for the external script
        var categoryColors = {
            @foreach(\App\Models\Category::where('user_id', Auth::id())->get() as $category)
                '{{ $category->name }}': '{{ $category->color }}',
            @endforeach
            'default': '#20ac82'
        };
        
        // Define calendar events data for the external script
        var calendarEvents = [
            @foreach ($upcomingTasks as $task)
            {
                id: {{ $task->id }},
                title: '{{ $task->title }}',
                start: '{{ $task->due_date }}',
                color: '{{ $task->urgency === 'high' ? '#dc3545' : ($task->urgency === 'medium' ? '#ffc107' : '#20ac82') }}',
                display: 'block',
                textColor: 'var(--primary)',
                extendedProps: {
                    categories: [
                        @foreach($task->categories as $category)
                            '{{ $category->name }}',
                        @endforeach
                    ],
                    description: '{{ $task->description ?? "Sem descrição" }}',
                    urgency: '{{ $task->urgency === "high" ? "Alta" : ($task->urgency === "medium" ? "Média" : "Baixa") }}',
                    editUrl: '{{ route('tasks.edit', $task->id) }}',
                    deleteUrl: '{{ route('tasks.destroy', $task->id) }}',
                    completeUrl: '{{ route('tasks.complete', $task->id) }}',
                    subtasks: [
                        @foreach($task->subtasks as $subtask)
                            {
                                id: {{ $subtask->id }},
                                description: '{{ addslashes($subtask->description) }}'
                            },
                        @endforeach
                    ]
                }
            },
            @endforeach
        ];
    </script>
    <script src="{{ asset('js/dashboard/script.js') }}"></script>
    <script src="{{ asset('js/script.js') }}"></script>
</div>

@include('layouts.footer')

<button id="theme-toggle" class="theme-toggle" aria-label="Toggle dark/light mode">
    <i class="fas fa-moon"></i>
</button>

@endsection

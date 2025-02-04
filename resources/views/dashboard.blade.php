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
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-user-edit me-2"></i>
                        Editar Perfil
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
                        <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'pt-br',
                events: [
                    @foreach ($upcomingTasks as $task)
                    {
                        title: '{{ $task->title }}',
                        start: '{{ $task->due_date }}',
                        url: '{{ route('tasks.edit', $task->id) }}',
                        color: '{{ $task->urgency === 'high' ? '#dc3545' : ($task->urgency === 'medium' ? '#ffc107' : '#0d6efd') }}'
                    },
                    @endforeach
                ]
            });
            calendar.render();
        });
    </script>
</div>
@endsection

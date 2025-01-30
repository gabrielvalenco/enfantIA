@extends('layouts.app')

@section('content')
<header class="dashboard-header">
    <div class="dashboard-header-content">
        <h1 class="dashboard-title">
            <i class="fas fa-tachometer-alt"></i>
            Painel de Controle
        </h1>
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
</div>

<style>
.dashboard-header {
    background: linear-gradient(135deg, var(--primary-color), #2980b9);
    color: white;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.dashboard-header-content {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.dashboard-title {
    font-size: 1.75rem;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.user-icon {
    width: 48px;
    height: 48px;
    background: rgba(255, 255, 255, 0.2);
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.status-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.status-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    transition: transform 0.2s ease;
}

.status-card:hover {
    transform: translateY(-2px);
}

.status-card-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.status-card-icon.pending { background-color: #3498db; }
.status-card-icon.completed { background-color: #2ecc71; }
.status-card-icon.urgent { background-color: #e74c3c; }
.status-card-icon.categories { background-color: #9b59b6; }

.status-card-info h3 {
    font-size: 1.5rem;
    margin: 0;
    font-weight: 600;
}

.status-card-info p {
    margin: 0;
    color: #666;
    font-size: 0.875rem;
}

.dashboard-section-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.dashboard-section-title i {
    color: var(--primary-color);
}

.menu-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}

.menu-item {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1.25rem;
    color: #2c3e50;
    text-decoration: none;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.menu-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    text-decoration: none;
    color: var(--primary-color);
}

.menu-item-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: var(--primary-color);
    transition: all 0.2s ease;
}

.menu-item:hover .menu-item-icon {
    color: white;
    background: var(--primary-color);
}

.menu-item-content h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
}

.menu-item-content p {
    margin: 0.25rem 0 0;
    font-size: 0.875rem;
    color: #666;
}

.upcoming-tasks {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.upcoming-tasks .table {
    margin: 0;
}

.upcoming-tasks .table th {
    border-top: none;
    font-weight: 600;
    color: #2c3e50;
}

.upcoming-tasks .table td {
    vertical-align: middle;
}

.upcoming-tasks .table tr:last-child td {
    border-bottom: none;
}

@media (max-width: 768px) {
    .status-cards {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }

    .menu-grid {
        grid-template-columns: 1fr;
    }

    .dashboard-header {
        padding: 1.5rem;
    }

    .status-card {
        padding: 1rem;
    }

    .upcoming-tasks {
        padding: 1rem;
    }

    .upcoming-tasks .table {
        padding: 0;
    }

    .upcoming-tasks .table th, .upcoming-tasks .table td {
        padding: 0.5rem;
    }
}
</style>
@endsection

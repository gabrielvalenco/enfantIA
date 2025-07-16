@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('css/dashboard-style.css') }}">

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>

    <header class="dashboard-header">
        <div class="dashboard-header-content">
            <h1 class="dashboard-title">{{ env('APP_NAME') }}</h1>
            
            <div class="header-right">
                <div class="history-icon" title="Histórico de ações" data-bs-toggle="modal" data-bs-target="#activityLogModal">
                    <i class="fas fa-history"></i>
                </div>
                
                <a href="{{ route('notifications.index') }}" 
                   class="notification-icon {{ isset($pendingInvitations) && $pendingInvitations->count() > 0 && (!session('notifications_seen') || session('last_notification_count') < $pendingInvitations->count()) ? 'pulse-animation' : '' }}" 
                   title="Notificações" 
                   id="notificationIcon" 
                   data-count="{{ isset($pendingInvitations) ? $pendingInvitations->count() : 0 }}"
                   data-seen="{{ session('notifications_seen') ? 'true' : 'false' }}"
                   data-last-count="{{ session('last_notification_count') ?? 0 }}">
                    <i class="fas fa-bell"></i>
                </a>
                
                <div class="dropdown">
                    <button class="btn btn-link text-light p-0 border-0" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="user-image rounded-circle">
                        @else
                            <div class="user-image rounded-circle d-flex justify-content-center align-items-center">
                                <i class="fas fa-user theme-icon"></i>
                            </div>
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end user-dropdown" aria-labelledby="userDropdown">
                        <li>
                            <div class="dropdown-header">
                                <span class="fw-bold">{{ Auth::user()->name }}</span>
                                <br>
                                <small class="text-muted">{{ Auth::user()->email }}</small>
                                @if(session('remembered'))
                                    <div class="remembered-badge" title="Conectado com a opção 'Lembrar-me' ativa">
                                        <i class="fas fa-check-circle"></i> Sessão lembrada
                                    </div>
                                @endif
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a href="{{ route('profile.index') }}" class="dropdown-item">
                                <i class="fas fa-user-edit me-2"></i>
                                Perfil
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
        </div>
    </header>
    <div class="scroll-progress-container">
        <div class="scroll-progress-bar" id="scrollProgressBar"></div>
    </div>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="cancel-button" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="status-cards">

            <div class="status-card">
                <div class="status-card-header">
                    <div class="status-card-icon urgent">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <h3>{{ $urgentTasks ?? 0 }}</h3>
                </div>
                <div class="status-card-info">
                    <p class="status-card-text">Tarefas Urgentes</p>
                </div>
            </div>

            <div class="status-card">
                <div class="status-card-header">
                    <div class="status-card-icon pending">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h3>{{ $pendingTasks ?? 0 }}</h3>
                </div>
                <div class="status-card-info">
                    <p class="status-card-text">Tarefas Pendentes</p>
                </div>
            </div>
            
            <div class="status-card">
                <div class="status-card-header">
                    <div class="status-card-icon completed">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>0</h3>
                </div>
                <div class="status-card-info">
                    <p class="status-card-text">Tarefas de Grupos</p>
                </div>
            </div>
            
            <div class="status-card">
                <div class="status-card-header">
                    <div class="status-card-icon categories">
                        <i class="fas fa-tags"></i>
                    </div>
                    <h3>{{ $categories ?? 0 }}</h3>
                </div>
                <div class="status-card-info">
                    <p class="status-card-text">Categorias</p>
                </div>
            </div>
        </div>

        <div class="dashboard-menu">
            <div class="dashboard-section-title">
                <i class="fas fa-compass"></i>
                Navegação Rápida
            </div>

            <div class="menu-grid-modern">
                <a href="{{ route('tasks.create') }}" class="menu-card">
                    <div class="menu-card-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="menu-card-content">
                        <h3>Criar Tarefa</h3>
                        <p>Adicione uma nova tarefa ao sistema</p>
                    </div>
                </a>

                <a href="{{ route('tasks.index') }}" class="menu-card">
                    <div class="menu-card-icon">
                        <i class="fas fa-list-ul"></i>
                    </div>
                    <div class="menu-card-content">
                        <h3>Tarefas Pendentes</h3>
                        <p>Visualize e gerencie suas tarefas ativas</p>
                    </div>
                </a>

                <a href="{{ route('tasks.completed') }}" class="menu-card">
                    <div class="menu-card-icon">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <div class="menu-card-content">
                        <h3>Tarefas Concluídas</h3>
                        <p>Veja o histórico de tarefas finalizadas</p>
                    </div>
                </a>

                <a href="{{ route('categories.index') }}" class="menu-card">
                    <div class="menu-card-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div class="menu-card-content">
                        <h3>Categorias</h3>
                        <p>Gerencie as categorias de tarefas</p>
                    </div>
                </a>

                <a href="{{ route('reports.index') }}" class="menu-card menu-card-highlight">
                    <div class="menu-card-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="menu-card-content">
                        <h3>Relatório de Desempenho</h3>
                        <p>Melhore sua produtividade com o birdIA</p>
                    </div>
                </a>
            </div>
        </div>

        <script src="{{ asset('js/script.js') }}"></script>
        <script src="{{ asset('js/dashboard/script.js') }}"></script>
        <script src="{{ asset('js/notifications.js') }}"></script>
        <script src="{{ asset('js/scroll-progress.js') }}"></script>
        <script src="{{ asset('js/activity-log/script.js') }}"></script>
        <script>
            // Define category colors for the external script
            var categoryColors = {
                @foreach(\App\Models\Category::where('user_id', Auth::id())->get() as $category)
                    '{{ addslashes($category->name) }}': '{{ $category->color }}',
                @endforeach
                'default': '#20ac82'
            };
        </script>
    </div>

    <!-- Activity Log Modal -->
    <div class="modal fade" id="activityLogModal" tabindex="-1" aria-labelledby="activityLogModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="activityLogModalLabel"><i class="fas fa-history me-2"></i>Histórico de Ações</h5>
                </div>
                <div class="modal-body">
                    <div id="activity-log-container" class="position-relative">
                        <div id="activity-log-loading" class="d-flex justify-content-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Carregando...</span>
                            </div>
                        </div>
                        <ul id="activity-log-list">
                            <!-- Activity logs will be populated here via JavaScript -->
                        </ul>
                        <div id="activity-log-empty" class="text-center py-4 d-none">
                            <p class="text-muted">Nenhuma atividade registrada.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="cancel-button" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.footer')

    <button id="theme-toggle" class="theme-toggle" aria-label="Toggle dark/light mode">
        <i class="fas fa-moon"></i>
    </button>
@endsection

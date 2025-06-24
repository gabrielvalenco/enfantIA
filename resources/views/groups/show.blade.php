@extends('layouts.app')

@section('content')

<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<link rel="stylesheet" href="{{ asset('css/group/show.css') }}">

<div class="container">
    <div class="group-header">
        <div class="group-title">
            <i class="fas fa-users group-icon"></i>
            <h1>{{ $group->name }}</h1>
        </div>
        <div class="group-actions">
            <a href="{{ route('groups.index') }}" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            @if($group->isAdmin(Auth::user()))
                <button type="button" class="btn btn-add-member" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                    <i class="fas fa-user-plus"></i> Adicionar Membro
                </button>
            @endif
            @if($group->isAdmin(Auth::user()))
            <button type="button" class="btn btn-delete" id="delete-group-btn">
                <i class="fas fa-trash"></i> Excluir Grupo
            </button>
            @else
            <button type="button" class="btn btn-leave" id="leave-group-btn">
                <i class="fas fa-sign-out-alt"></i> Sair do Grupo
            </button>
            @endif
        </div>
    </div>

    <div class="group-content">
        <div class="tasks-container">
            <!-- Tarefas do Grupo -->
            <div class="card tasks-card">
                <div class="card-header">
                    <div class="header-content">
                        <i class="fas fa-tasks"></i>
                        <h2>Tarefas do Grupo</h2>
                    </div>
                    <a href="{{ route('tasks.create', ['group_id' => $group->id]) }}" class="btn btn-new-task">
                        <i class="fas fa-plus"></i> Nova Tarefa
                    </a>
                </div>
                <div class="card-body">
                    @forelse($tasks as $task)
                        <div class="task-item {{ $task->completed ? 'task-completed' : '' }}" 
             data-task-id="{{ $task->id }}" 
             data-task-creator="{{ $task->creator->name }}" 
             data-task-date="{{ $task->formatted_due_date }}" 
             data-task-description="{{ $task->description }}" 
             data-task-completed="{{ $task->completed ? '1' : '0' }}" 
             @if ($task->assignedUser) data-task-assignee="{{ $task->assignedUser->name }}" @endif>
                            <div class="task-content">
                                <h3 class="task-title">{{ $task->title }}</h3>
                                <div class="task-meta">
                                    <span class="task-creator">
                                        <i class="fas fa-user"></i> {{ $task->creator->name }}
                                    </span>
                                    <span class="task-date">
                                        <i class="fas fa-calendar"></i> {{ $task->due_date->format('d/m/Y') }}
                                    </span>
                                    @if($task->assignedUser)
                                    <span class="task-assignee">
                                        <i class="fas fa-user-check"></i> {{ $task->assignedUser->name }}
                                    </span>
                                    @endif
                                </div>
                                <p class="task-description">{{ $task->description }}</p>
                                <div class="task-actions">
                                    @if(!$task->completed)
                                        <form action="{{ route('tasks.complete', $task) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-complete" title="Concluir">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-edit" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-delete-task" title="Excluir"
                                            onclick="return confirm('Tem certeza que deseja excluir esta tarefa?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-tasks">
                            <i class="fas fa-info-circle"></i>
                            <p>Nenhuma tarefa cadastrada para este grupo ainda.</p>
                        </div>
                    @endforelse
                    
                    
                </div>
            </div>
        </div>

        <div class="info-container">
            <!-- Informações do Grupo -->
            <div class="card info-card">
                <div class="card-header">
                    <div class="header-content">
                        <i class="fas fa-info-circle"></i>
                        <h2>Informações do Grupo</h2>
                    </div>
                </div>
                <div class="card-body">
                    @if($group->description)
                        <div class="group-description">
                            <p>{{ $group->description }}</p>
                        </div>
                    @endif
                    <div class="members-section">
                        <h3>Membros:</h3>
                        <div class="members-list">
                            @foreach($members as $member)
                                <div class="member-item">
                                    <div class="member-info">
                                        <span class="member-name">{{ $member->name }}</span>
                                        @if($group->isAdmin($member))
                                            <span class="admin-badge">
                                                <i class="fas fa-crown"></i> Admin
                                            </span>
                                        @endif
                                    </div>
                                    @if($group->isAdmin(Auth::user()) && !$group->isAdmin($member) && $member->id !== Auth::id())
                                        <form action="{{ route('groups.remove-member', $group) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="user_id" value="{{ $member->id }}">
                                            <button type="submit" class="btn btn-remove-member" 
                                                onclick="return confirm('Tem certeza que deseja remover este membro?')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="card info-card" id="group-tasks-card">
                <div class="card-header">
                    <div class="header-content">
                        <i class="fas fa-list-alt"></i>
                        <h2>Tarefas do Grupo</h2>
                    </div>
                </div>
                <div class="card-body text-center">
                    <div class="view-all-tasks-container">
                        <i class="fas fa-tasks view-all-icon"></i>
                        <p class="view-all-text">Visualize todas as tarefas do grupo em detalhes, com filtros por responsável e outras opções.</p>
                        <a href="{{ route('tasks.index', ['group_id' => $group->id]) }}" class="btn btn-view-all">
                            <i class="fas fa-eye"></i> Ver Todas as Tarefas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Adicionar Membro -->
@if($group->isAdmin(Auth::user()))
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMemberModalLabel">Adicionar Novo Membro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('groups.add-member', $group) }}" method="POST" id="addMemberForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email do novo membro</label>
                        <div class="input-group">
                            <input type="email" class="form-control" id="email" name="email" autocomplete="off">
                            <button type="button" class="btn btn-check-email" id="checkEmailBtn">Verificar</button>
                        </div>
                        <div id="emailFeedback" class="mt-2"></div>
                    </div>
                    
                    <div class="info-message">
                        <i class="fas fa-info-circle"></i> Digite o email do usuário que deseja convidar para o grupo.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-confirm" id="addMemberBtn" disabled>Adicionar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Toast Notifications Container -->
<div class="toast-container"></div>

<!-- Modal de Detalhes da Tarefa -->
<div class="modal fade" id="taskDetailsModal" tabindex="-1" aria-labelledby="taskDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskDetailsModalLabel"><i class="fas fa-tasks me-2"></i>Detalhes da Tarefa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="task-details-container">
                    <div class="task-header">
                        <h3 id="modal-task-title"></h3>
                        <span id="modal-task-status" class=""></span>
                    </div>
                    
                    <div class="task-info">
                        <div class="info-grid">
                            <div class="info-row">
                                <span class="info-label"><i class="fas fa-user"></i> Criado por:</span>
                                <span id="modal-task-creator" class="info-value"></span>
                            </div>
                            
                            <div class="info-row">
                                <span class="info-label"><i class="fas fa-calendar"></i> Data de vencimento:</span>
                                <span id="modal-task-date" class="info-value"></span>
                            </div>
                            
                            <div class="info-row" id="modal-assignee-row">
                                <span class="info-label"><i class="fas fa-user-check"></i> Responsável:</span>
                                <span id="modal-task-assignee" class="info-value"></span>
                            </div>
                        </div>
                        
                        <div class="description-section">
                            <div class="description-header">
                                <i class="fas fa-align-left"></i> Descrição
                            </div>
                            <div class="description-content">
                                <p id="modal-task-description"></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="task-modal-actions">
                        <div id="modal-complete-action"></div>
                        <div id="modal-edit-action"></div>
                        <div id="modal-delete-action"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Pass PHP variables to JavaScript
    const groupId = {{ $group->id }};
    const csrfTokenValue = "{{ csrf_token() }}";
    const userEmail = "{{ Auth::user()->email }}";
</script>
<script src="{{ asset('js/group/show.js') }}"></script>
@endpush
@endsection

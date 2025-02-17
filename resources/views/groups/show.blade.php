@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/group-style.css') }}">

<div class="container">
    <div class="dashboard-section-title mb-4">
        <h1 class="d-inline-block mb-4">
            <i class="fas fa-users"></i>
            {{ $group->name }}
        </h1>
        <div class="float-end">
            <a href="{{ route('groups.index') }}" class="btn btn-secondary btn-sm me-2">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            @if($group->isAdmin(Auth::user()))
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                    <i class="fas fa-user-plus"></i> Adicionar Membro
                </button>
            @endif
            @if($group->isAdmin(Auth::user()))
            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteGroupModal">
                <i class="fas fa-trash"></i> Excluir Grupo
            </button>
            @else
            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#leaveGroupModal">
                <i class="fas fa-sign-out-alt"></i> Sair do Grupo
            </button>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Tarefas do Grupo -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-tasks"></i> Tarefas do Grupo
                    </h5>
                    <a href="{{ route('tasks.create', ['group_id' => $group->id]) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nova Tarefa
                    </a>
                </div>
                <div class="card-body">
                    @forelse($tasks as $task)
                        <div class="task-item p-3 mb-3 rounded @if($task->completed) bg-light text-muted @endif">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-1">{{ $task->title }}</h5>
                                    <p class="mb-1 text-muted">
                                        <small>
                                            <i class="fas fa-user"></i> {{ $task->creator->name }} |
                                            <i class="fas fa-calendar"></i> {{ $task->due_date->format('d/m/Y') }} |
                                            <i class="fas fa-tag"></i> {{ $task->category->name ?? 'Sem categoria' }}
                                        </small>
                                    </p>
                                    <p class="mb-0">{{ $task->description }}</p>
                                </div>
                                <div class="task-actions">
                                    @if(!$task->completed)
                                        <form action="{{ route('tasks.complete', $task) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success btn-sm" title="Concluir">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-warning btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Excluir"
                                            onclick="return confirm('Tem certeza que deseja excluir esta tarefa?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Nenhuma tarefa cadastrada para este grupo ainda.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Informações do Grupo -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle"></i> Informações do Grupo
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">{{ $group->description }}</p>
                    <hr>
                    <h6>Membros:</h6>
                    <div class="list-group">
                        @foreach($members as $member)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $member->name }}
                                <div>
                                    @if($group->isAdmin($member))
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-crown"></i> Admin
                                        </span>
                                    @endif
                                    @if($group->isAdmin(Auth::user()) && !$group->isAdmin($member) && $member->id !== Auth::id())
                                        <form action="{{ route('groups.remove-member', $group) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="user_id" value="{{ $member->id }}">
                                            <button type="submit" class="btn btn-danger btn-sm" 
                                                onclick="return confirm('Tem certeza que deseja remover este membro?')">
                                                <i class="fas fa-user-minus"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
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
            <form action="{{ route('groups.add-member', $group) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email do novo membro</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Adicionar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Modal Excluir Grupo -->
<div class="modal fade" id="deleteGroupModal" tabindex="-1" aria-labelledby="deleteGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteGroupModalLabel">Excluir Grupo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('groups.delete', $group) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir este grupo?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

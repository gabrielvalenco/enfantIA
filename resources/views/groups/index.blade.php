@extends('layouts.app')

@section('content')

<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<link rel="stylesheet" href="{{ asset('css/group-style.css') }}">

<div class="container">
    <div class="dashboard-section-title mb-4">
        <h1 class="d-inline-block mb-4">
            <i class="fas fa-users mb-4"></i>
            <h1 class="d-inline-block ps-2">Meus Grupos</h1>
        </h1>
        <div class="float-end">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm me-2">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <a href="{{ route('groups.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo Grupo
            </a>
        </div>
    </div>

    <div class="row">
        @forelse ($groups as $group)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-users text-primary"></i>
                            {{ $group->name }}
                        </h5>
                        <p class="card-text text-muted">
                            {{ Str::limit($group->description, 100) }}
                        </p>
                        <div class="mt-3">
                            <strong class="text-muted">Membros:</strong>
                            <div class="mt-2">
                                @foreach($group->members as $member)
                                    <span class="badge bg-light text-dark me-1">
                                        {{ $member->name }}
                                        @if($group->isAdmin($member))
                                            <i class="fas fa-crown text-warning"></i>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                            @if($group->isAdmin(Auth::user()))
                                <a href="{{ route('groups.delete', $group) }}" class="btn btn-danger btn-sm me-2">
                                    <i class="fas fa-trash"></i> Excluir
                                </a>
                            @else
                                <a href="{{ route('groups.leave', $group) }}" class="btn btn-danger btn-sm me-2">
                                    <i class="fas fa-sign-out-alt"></i> Sair
                                </a>
                            @endif
                        <a href="{{ route('groups.show', $group) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye"></i> Ver Grupo
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Você ainda não participa de nenhum grupo. Crie um novo grupo ou peça para ser adicionado em um existente!
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')

<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<link rel="stylesheet" href="{{ asset('css/group/index.css') }}">

<div class="container mt-4">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-header">
        <h1><i class="fas fa-users mr-2"></i> Meus Grupos</h1>
        <div class="table-actions">
            <a href="{{ route('dashboard') }}" class="back-button">
                Voltar ao Dashboard
            </a>
            <a href="{{ route('groups.create') }}" class="add-group-button">
                <i class="fas fa-plus-circle"></i> Novo Grupo
            </a>
        </div>
    </div>

    <div class="groups-container">
        <div class="row">
            @forelse ($groups as $group)
                <div class="col-md-4 mb-4">
                    <div class="card group-card h-100">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-users text-primary mr-2"></i>
                                {{ $group->name }}
                            </h5>
                            <p class="card-text">
                                {{ Str::limit($group->description, 100) }}
                            </p>
                            <div class="mt-3 members-section">
                                <strong>Membros:</strong>
                                <div class="mt-2 members-list">
                                    @foreach($group->members as $member)
                                        <span class="member-badge mr-1 mb-1">
                                            {{ $member->name }}
                                            @if($group->isAdmin($member))
                                                <i class="fas fa-crown text-warning ml-1"></i>
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="action-buttons">
                                <a href="{{ route('groups.show', $group) }}" class="btn btn-info btn-sm action-btn">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                                @if($group->isAdmin(Auth::user()))
                                    <button type="button" class="btn btn-danger btn-sm action-btn delete-group-btn" data-group-id="{{ $group->id }}">
                                        <i class="fas fa-trash"></i> Excluir
                                    </button>
                                @else
                                    <button type="button" class="btn btn-danger btn-sm action-btn leave-group-btn" data-group-id="{{ $group->id }}">
                                        <i class="fas fa-sign-out-alt"></i> Sair
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        Você ainda não participa de nenhum grupo. Crie um novo grupo ou peça para ser adicionado em um existente!
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Script personalizado para grupos -->
<script src="{{ asset('js/group/script.js') }}"></script>
@endpush

@endsection

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="dashboard-section-grou-title mb-4">
        <h1 class="d-inline-block mb-4">
            <i class="fas fa-envelope"></i> Convites de Grupos
        </h1>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bell"></i> Convites Pendentes
                    </h5>
                </div>
                <div class="card-body">
                    @if($pendingInvitations->count() > 0)
                        <div class="list-group">
                            @foreach($pendingInvitations as $invitation)
                                <div class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-1">{{ $invitation->group->name }}</h5>
                                            <p class="mb-1">
                                                <small class="text-muted">
                                                    <i class="fas fa-user"></i> Criado por: {{ $invitation->group->creator->name }}
                                                </small>
                                            </p>
                                            <p class="mb-1">{{ $invitation->group->description }}</p>
                                            <p class="mb-0">
                                                <small class="text-muted">
                                                    <i class="fas fa-clock"></i> Convite recebido em: {{ $invitation->created_at->format('d/m/Y H:i') }}
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
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Você não possui convites pendentes.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

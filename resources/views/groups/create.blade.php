@extends('layouts.app')

@section('content')

<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<link rel="stylesheet" href="{{ asset('css/group-style.css') }}">
<link rel="stylesheet" href="{{ asset('css/dashboard-style.css') }}">
<link rel="stylesheet" href="{{ asset('css/group/create.css') }}">
<meta name="user-email" content="{{ Auth::user()->email }}">

<div class="container mt-4">
    <!-- Toast container -->
    <div class="toast-container"></div>
    
    <div class="dashboard-section-title mb-4">
        <i class="fas fa-users"></i>
        <h2>Gerenciamento de Grupos</h2>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card group-card">
                <div class="card-header p-0">
                    <ul class="nav nav-tabs" id="groupTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="create-tab" data-bs-toggle="tab" 
                                data-bs-target="#create-tab-pane" type="button" role="tab" 
                                aria-controls="create-tab-pane" aria-selected="true">
                                <i class="fas fa-plus-circle me-2"></i>Criar Grupo
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="settings-tab" data-bs-toggle="tab" 
                                data-bs-target="#settings-tab-pane" type="button" role="tab" 
                                aria-controls="settings-tab-pane" aria-selected="false">
                                <i class="fas fa-cog me-2"></i>Configurações
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body tab-content" id="groupTabsContent">
                    <!-- Tab de Criação de Grupo -->
                    <div class="tab-pane fade show active" id="create-tab-pane" role="tabpanel" 
                        aria-labelledby="create-tab" tabindex="0">
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="group-info-container">
                                    <form class="group-form" action="{{ route('groups.store') }}" method="POST" id="groupForm">
                                        @csrf

                                        <div class="mb-4">
                                            <label for="name" class="form-label">Nome do grupo</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                id="name" name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-4">
                                            <label for="description" class="form-label">Descrição do grupo (Opcional)</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                                id="description" name="description" rows="4">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-4">
                                            <label class="form-label">Adicione membros</label>
                                            <div class="input-group">
                                                <input type="email" id="memberEmail" class="form-control" 
                                                    placeholder="email@exemplo.com">
                                                <button type="button" class="btn btn-add-member" id="addMemberBtn">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                            
                                            <!-- Container para os emails adicionados -->
                                            <div id="email-tags-container" class="mt-3"></div>
                                            
                                            <!-- Campo oculto para armazenar os emails -->
                                            <input type="hidden" name="members" id="membersInput" value="">
                                        </div>

                                        <div class="d-grid gap-3 mt-5">
                                            <button type="submit" class="btn btn-primary btn-lg create-btn">
                                                Criar grupo
                                            </button>
                                            <a href="{{ route('groups.index') }}" class="btn btn-outline-secondary back-button">
                                                Voltar
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab de Configurações -->
                    <div class="tab-pane fade" id="settings-tab-pane" role="tabpanel" 
                        aria-labelledby="settings-tab" tabindex="0">
                        <div class="settings-container p-3">
                            <div class="settings-placeholder text-center py-5">
                                <i class="fas fa-cog fa-4x mb-3 text-muted"></i>
                                <h4 class="text-muted">Configurações avançadas</h4>
                                <p class="text-muted">Este painel será implementado em breve.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/group/create.js') }}"></script>
@endpush
@endsection

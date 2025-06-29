@extends('layouts.app')

@section('content')

<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<link rel="stylesheet" href="{{ asset('css/group-style.css') }}">
<link rel="stylesheet" href="{{ asset('css/dashboard-style.css') }}">
<link rel="stylesheet" href="{{ asset('css/group/create.css') }}">
<link rel="stylesheet" href="{{ asset('css/group/settings.css') }}">
<meta name="user-email" content="{{ Auth::user()->email }}">
<meta name="csrf-token" content="{{ csrf_token() }}">

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
                        <div class="settings-container p-4">
                            <h4 class="mb-4"><i class="fas fa-cog me-2"></i>Configurações do Grupo</h4>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- Modo Competitivo -->
                                    <div class="card mb-4 settings-card">
                                        <div class="card-header d-flex align-items-center">
                                            <i class="fas fa-trophy me-2 text-warning"></i>
                                            <h5 class="mb-0">Modo Competitivo</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="competitiveMode">
                                                <label class="form-check-label" for="competitiveMode">Ativar modo competitivo</label>
                                            </div>
                                            <p class="text-muted">O modo competitivo exibe um ranking dos membros que mais completaram tarefas no grupo.</p>
                                            
                                            <div class="competitive-preview mt-3 p-3 border rounded bg-light">
                                                <h6 class="mb-3">Prévia do Ranking</h6>
                                                <div class="ranking-list">
                                                    <div class="ranking-item d-flex align-items-center mb-2">
                                                        <div class="rank-badge rank-1 me-2">1</div>
                                                        <div class="rank-avatar me-2">
                                                            <i class="fas fa-user-circle"></i>
                                                        </div>
                                                        <div class="rank-info">
                                                            <span class="rank-name">Maria Silva</span>
                                                            <div class="rank-stats">
                                                                <span class="badge bg-success">32 tarefas</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="ranking-item d-flex align-items-center mb-2">
                                                        <div class="rank-badge rank-2 me-2">2</div>
                                                        <div class="rank-avatar me-2">
                                                            <i class="fas fa-user-circle"></i>
                                                        </div>
                                                        <div class="rank-info">
                                                            <span class="rank-name">João Costa</span>
                                                            <div class="rank-stats">
                                                                <span class="badge bg-success">28 tarefas</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="ranking-item d-flex align-items-center">
                                                        <div class="rank-badge rank-3 me-2">3</div>
                                                        <div class="rank-avatar me-2">
                                                            <i class="fas fa-user-circle"></i>
                                                        </div>
                                                        <div class="rank-info">
                                                            <span class="rank-name">Ana Paula</span>
                                                            <div class="rank-stats">
                                                                <span class="badge bg-success">21 tarefas</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Permissões de Membros -->
                                    <div class="card mb-4 settings-card">
                                        <div class="card-header d-flex align-items-center">
                                            <i class="fas fa-users-cog me-2 text-primary"></i>
                                            <h5 class="mb-0">Permissões de Membros</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="permission-option mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="allowMembersInvite">
                                                    <label class="form-check-label" for="allowMembersInvite">
                                                        <strong>Membros podem adicionar novos membros</strong>
                                                    </label>
                                                </div>
                                                <p class="text-muted ms-4">Se ativado, todos os membros podem enviar convites para novas pessoas ingressarem no grupo.</p>
                                            </div>
                                            
                                            <div class="permission-option mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="allowMembersCreateTasks">
                                                    <label class="form-check-label" for="allowMembersCreateTasks">
                                                        <strong>Membros podem criar tarefas</strong>
                                                    </label>
                                                </div>
                                                <p class="text-muted ms-4">Se ativado, todos os membros podem criar novas tarefas para o grupo.</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Botões de Ação -->
                                    <div class="d-flex justify-content-end mt-4">
                                        <button class="btn btn-primary" id="saveSettings">
                                            <i class="fas fa-save me-2"></i>Salvar Configurações
                                        </button>
                                    </div>
                                </div>
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
<script src="{{ asset('js/group/settings.js') }}"></script>
@endpush
@endsection

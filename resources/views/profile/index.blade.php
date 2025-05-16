@extends('layouts.app')

@section('content')

<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<link rel="stylesheet" href="{{ asset('css/profile-style.css') }}">

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header rounded-0 bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white">Meu Perfil</h5>
                    <a href="{{ route('dashboard') }}">
                        Voltar ao Dashboard
                    </a>
                </div>

                <div class="card-body mt-4">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="row">
                            <div class="col-md-4 text-center">
                                <div class="avatar-wrapper">
                                    <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/default-avatar.png') }}" 
                                         alt="Avatar" class="img-fluid mb-3" style="object-fit: cover;">
                                    <div class="mt-2">
                                        <label for="avatar" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-camera me-2"></i>Alterar Foto
                                        </label>
                                        <input type="file" name="avatar" id="avatar" class="d-none" accept="image/*">
                                    </div>
                                </div>
                            </div>

                            <div class="profile-info col-md-8">
                                <div class="form-group mb-3">
                                    <label for="name">Nome</label>
                                    <input type="text" name="name" id="name" class="form-profile @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="email">E-mail</label>
                                    <input type="email" name="email" id="email" class="form-profile @error('email') is-invalid @enderror" 
                                           value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="phone">Telefone</label>
                                    <input type="tel" name="phone" id="phone" class="form-profile @error('phone') is-invalid @enderror" 
                                           value="{{ old('phone', $user->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="position">Cargo/Posição</label>
                                    <select name="position" id="position" class="form-profile position-select @error('position') is-invalid @enderror">
                                        <option value="" disabled {{ old('position', $user->position) ? '' : 'selected' }}>Selecione um cargo</option>
                                        <option value="Gerente de Projetos" {{ old('position', $user->position) == 'Gerente de Projetos' ? 'selected' : '' }}>Gerente de Projetos</option>
                                        <option value="Desenvolvedor" {{ old('position', $user->position) == 'Desenvolvedor' ? 'selected' : '' }}>Desenvolvedor</option>
                                        <option value="Designer" {{ old('position', $user->position) == 'Designer' ? 'selected' : '' }}>Designer</option>
                                        <option value="Analista de Sistemas" {{ old('position', $user->position) == 'Analista de Sistemas' ? 'selected' : '' }}>Analista de Sistemas</option>
                                        <option value="Product Owner" {{ old('position', $user->position) == 'Product Owner' ? 'selected' : '' }}>Product Owner</option>
                                        <option value="Scrum Master" {{ old('position', $user->position) == 'Scrum Master' ? 'selected' : '' }}>Scrum Master</option>
                                        <option value="QA/Tester" {{ old('position', $user->position) == 'QA/Tester' ? 'selected' : '' }}>QA/Tester</option>
                                        <option value="Coordenador" {{ old('position', $user->position) == 'Coordenador' ? 'selected' : '' }}>Coordenador</option>
                                        <option value="Diretor" {{ old('position', $user->position) == 'Diretor' ? 'selected' : '' }}>Diretor</option>
                                        <option value="Outro" {{ !in_array(old('position', $user->position), ['', 'Gerente de Projetos', 'Desenvolvedor', 'Designer', 'Analista de Sistemas', 'Product Owner', 'Scrum Master', 'QA/Tester', 'Coordenador', 'Diretor']) && old('position', $user->position) ? 'selected' : '' }}>Outro</option>
                                    </select>
                                    <input type="text" name="custom_position" id="custom_position" class="form-profile mt-2" placeholder="Especifique seu cargo" style="display: {{ !in_array(old('position', $user->position), ['', 'Gerente de Projetos', 'Desenvolvedor', 'Designer', 'Analista de Sistemas', 'Product Owner', 'Scrum Master', 'QA/Tester', 'Coordenador', 'Diretor']) && old('position', $user->position) ? 'block' : 'none' }}; width: 100%;" value="{{ !in_array(old('position', $user->position), ['', 'Gerente de Projetos', 'Desenvolvedor', 'Designer', 'Analista de Sistemas', 'Product Owner', 'Scrum Master', 'QA/Tester', 'Coordenador', 'Diretor']) ? old('position', $user->position) : '' }}">
                                    @error('position')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="bio">Biografia</label>
                            <textarea name="bio" id="bio" rows="3" class="form-profile @error('bio') is-invalid @enderror" placeholder="Escreva sobre você aqui">{{ old('bio', $user->bio) }}</textarea>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="task-stats-section mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Estatísticas de Tarefas</h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="stat-item">
                                                <span class="stat-label">Tarefas concluídas este mês:</span>
                                                <span class="stat-value">{{ $user->completedTasks()->whereMonth('updated_at', now()->month)->whereYear('updated_at', now()->year)->count() }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="stat-item">
                                                <span class="stat-label">Tarefas concluídas esta semana:</span>
                                                <span class="stat-value">{{ $user->completedTasks()->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])->count() }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="stat-item">
                                                <span class="stat-label">Total de tarefas concluídas:</span>
                                                <span class="stat-value">{{ $user->completedTasks()->count() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit">
                                <i class="fas fa-save me-2"></i>Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>        
    </div>

<script src="{{ asset('js/profile/script.js') }}"></script>
@endsection

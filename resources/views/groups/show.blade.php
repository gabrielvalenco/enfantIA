@extends('layouts.app')

@section('content')

<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<link rel="stylesheet" href="{{ asset('css/group/unique-group.css') }}">

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
            <button type="button" class="btn btn-danger btn-sm" id="delete-group-btn">
                <i class="fas fa-trash"></i> Excluir Grupo
            </button>
            @else
            <button type="button" class="btn btn-danger btn-sm" id="leave-group-btn">
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
                        <div class="task-item p-3 mb-3 rounded @if($task->completed) text-muted @endif">
                            <div>
                                <h5 class="mb-1">{{ $task->title }}</h5>
                                <p class="mb-2 text-muted">
                                    <small>
                                        <i class="fas fa-user"></i> {{ $task->creator->name }} |
                                        <i class="fas fa-calendar"></i> {{ $task->due_date->format('d/m/Y') }}
                                        @if($task->assignedUser)
                                         | <i class="fas fa-user-check"></i> Responsável: {{ $task->assignedUser->name }}
                                        @endif
                                    </small>
                                </p>
                                <p class="mb-3">{{ $task->description }}</p>
                                <hr class="my-2">
                                <div class="task-actions text-end mt-2">
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
            <form action="{{ route('groups.add-member', $group) }}" method="POST" id="addMemberForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email do novo membro</label>
                        <div class="input-group">
                            <input type="email" class="form-control" id="email" name="email" autocomplete="off">
                            <button type="button" class="btn btn-primary" id="checkEmailBtn">Verificar</button>
                        </div>
                        <div id="emailFeedback" class="mt-2"></div>
                    </div>
                    
                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle"></i> Digite o email do usuário que deseja convidar para o grupo.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="addMemberBtn" disabled>Adicionar</button>
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

<!-- Modal Sair do Grupo -->
<div class="modal fade" id="leaveGroupModal" tabindex="-1" aria-labelledby="leaveGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="leaveGroupModalLabel">Sair do Grupo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('groups.leave', $group) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Tem certeza que deseja sair deste grupo?</p>
                    <p class="text-danger"><strong>Atenção:</strong> Você não terá mais acesso às tarefas e informações do grupo.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Saída</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Botão para excluir grupo
        const deleteGroupBtn = document.getElementById('delete-group-btn');
        if (deleteGroupBtn) {
            deleteGroupBtn.addEventListener('click', function() {
                Swal.fire({
                    title: 'Tem certeza?',
                    text: 'Esta ação excluirá permanentemente o grupo e todas as suas tarefas!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: 'var(--link-color)',
                    cancelButtonColor: 'var(--text-secondary)',
                    confirmButtonText: 'Sim, excluir!',
                    cancelButtonText: 'Cancelar',
                    background: getComputedStyle(document.documentElement).getPropertyValue('--surface-color'),
                    color: getComputedStyle(document.documentElement).getPropertyValue('--text-primary'),
                    customClass: {
                        confirmButton: 'swal-confirm-button',
                        cancelButton: 'swal-cancel-button',
                        title: 'swal-title',
                        htmlContainer: 'swal-html-container'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Criar e enviar formulário via JavaScript
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route("groups.delete", $group) }}';
                        form.style.display = 'none';
                        
                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';
                        form.appendChild(csrfToken);
                        
                        const methodField = document.createElement('input');
                        methodField.type = 'hidden';
                        methodField.name = '_method';
                        methodField.value = 'DELETE';
                        form.appendChild(methodField);
                        
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        }
        
        // Botão para sair do grupo
        const leaveGroupBtn = document.getElementById('leave-group-btn');
        if (leaveGroupBtn) {
            leaveGroupBtn.addEventListener('click', function() {
                Swal.fire({
                    title: 'Confirmar saída',
                    text: 'Tem certeza que deseja sair deste grupo? Você não terá mais acesso às tarefas e informações do grupo.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: 'var(--link-color)',
                    cancelButtonColor: 'var(--text-secondary)',
                    confirmButtonText: 'Sim, sair',
                    cancelButtonText: 'Cancelar',
                    background: getComputedStyle(document.documentElement).getPropertyValue('--surface-color'),
                    color: getComputedStyle(document.documentElement).getPropertyValue('--text-primary'),
                    customClass: {
                        confirmButton: 'swal-confirm-button',
                        cancelButton: 'swal-cancel-button',
                        title: 'swal-title',
                        htmlContainer: 'swal-html-container'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Criar e enviar formulário via JavaScript
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route("groups.leave", $group) }}';
                        form.style.display = 'none';
                        
                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';
                        form.appendChild(csrfToken);
                        
                        const methodField = document.createElement('input');
                        methodField.type = 'hidden';
                        methodField.name = '_method';
                        methodField.value = 'DELETE';
                        form.appendChild(methodField);
                        
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        }
        
        const emailInput = document.getElementById('email');
        const emailFeedback = document.getElementById('emailFeedback');
        const addMemberBtn = document.getElementById('addMemberBtn');
        const checkEmailBtn = document.getElementById('checkEmailBtn');
        const addMemberForm = document.getElementById('addMemberForm');
        const currentUserEmail = "{{ Auth::user()->email }}";
        const groupId = "{{ $group->id }}";
        
        // Inicialmente, desabilitar o botão de adicionar
        addMemberBtn.disabled = true;
        
        // Listener para o botão de verificar email
        checkEmailBtn.addEventListener('click', validateEmail);
        
        // Listener para limpar a validação quando o usuário digita
        emailInput.addEventListener('input', function() {
            emailInput.classList.remove('is-invalid', 'is-valid');
            emailFeedback.innerHTML = '';
            addMemberBtn.disabled = true; // Manter desabilitado até validar
        });
        
        // Listener para pressionar Enter no campo de email
        emailInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                validateEmail();
            }
        });
        
        // Prevenir submissão se o email for inválido
        addMemberForm.addEventListener('submit', function(e) {
            if (emailInput.classList.contains('is-invalid') || !emailInput.classList.contains('is-valid')) {
                e.preventDefault();
                
                if (!emailInput.value.trim()) {
                    showError('Por favor, insira um email para convidar.');
                } else if (!emailInput.classList.contains('is-valid')) {
                    showError('Por favor, verifique o email antes de adicionar.');
                }
            }
        });
        
        function validateEmail() {
            const email = emailInput.value.trim();
            
            // Reset validation
            emailInput.classList.remove('is-invalid', 'is-valid');
            emailFeedback.innerHTML = '';
            addMemberBtn.disabled = true;
            
            if (email === '') {
                showError('Por favor, insira um email para convidar.');
                return;
            }
            
            // Mostrar mensagem de carregamento
            emailFeedback.innerHTML = `<div class="alert alert-info"><i class="fas fa-spinner fa-spin"></i> Verificando email...</div>`;
            
            // Desabilitar inputs durante a verificação
            emailInput.disabled = true;
            checkEmailBtn.disabled = true;
            
            // Check if user is trying to invite themselves
            if (email.toLowerCase() === currentUserEmail.toLowerCase()) {
                showError('Você não pode convidar a si mesmo para o grupo.');
                emailInput.disabled = false;
                checkEmailBtn.disabled = false;
                return;
            }
            
            // Simple email validation
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                showError('Por favor, insira um email válido.');
                emailInput.disabled = false;
                checkEmailBtn.disabled = false;
                return;
            }
            
            // Check if user exists via AJAX
            fetch(`/api/check-user?email=${encodeURIComponent(email)}&group_id=${groupId}`)
                .then(response => response.json())
                .then(data => {
                    // Habilitar inputs novamente
                    emailInput.disabled = false;
                    checkEmailBtn.disabled = false;
                    
                    if (!data.exists) {
                        showError('Este usuário não existe no sistema.');
                    } else if (data.inGroup) {
                        showError('Este usuário já é membro do grupo.');
                    } else if (data.hasPendingInvitation) {
                        showError('Este usuário já possui um convite pendente para este grupo.');
                    } else {
                        showSuccess('Usuário encontrado! Clique em "Adicionar" para enviar o convite.');
                    }
                })
                .catch(error => {
                    console.error('Error checking user:', error);
                    showError('Ocorreu um erro ao verificar o usuário. Tente novamente.');
                    emailInput.disabled = false;
                    checkEmailBtn.disabled = false;
                });
        }
        
        function showError(message) {
            emailInput.classList.add('is-invalid');
            emailFeedback.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ${message}</div>`;
            addMemberBtn.disabled = true;
        }
        
        function showSuccess(message) {
            emailInput.classList.add('is-valid');
            emailFeedback.innerHTML = `<div class="alert alert-success"><i class="fas fa-check-circle"></i> ${message}</div>`;
            addMemberBtn.disabled = false;
        }
    });
</script>
@endpush

@endsection

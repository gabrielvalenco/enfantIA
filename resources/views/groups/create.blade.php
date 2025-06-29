@ -1,214 +0,0 @@
@extends('layouts.app')

@section('content')

<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<link rel="stylesheet" href="{{ asset('css/group-style.css') }}">

<div class="container">
    <div class="dashboard-section-title mb-4 d-flex justify-content-center">
        <i class="fas fa-users fa-2x"></i>
        <h1 class="d-inline-block ps-2">Criar Grupo</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form class="d-block" action="{{ route('groups.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Nome do Grupo</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Adicionar Membros (opcional - até 3)</label>
                            <div id="memberFields">
                                <div class="input-group mb-2">
                                    <input type="email" name="members[]" class="form-control" 
                                        placeholder="email@exemplo.com" onblur="validateEmail(this)">
                                    <button type="button" class="btn btn-success add-member" 
                                        onclick="addMemberField()" id="addMemberBtn">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted">
                                Digite o email dos membros que você deseja convidar para o grupo. 
                                Eles receberão um convite e precisarão aceitá-lo para entrar no grupo.
                            </small>
                        </div>

                        <div class="d-grid gap-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Criar Grupo
                            </button>
                            <a href="{{ route('groups.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Voltar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let memberCount = 1;
    const maxMembers = 3;
    const currentUserEmail = "{{ Auth::user()->email }}";

    function addMemberField() {
        if (memberCount < maxMembers) {
            memberCount++;
            const field = document.createElement('div');
            field.className = 'input-group mb-2';
            field.innerHTML = `
                <input type="email" name="members[]" class="form-control" placeholder="email@exemplo.com" onblur="validateEmail(this)">
                <button type="button" class="btn btn-danger" onclick="removeMemberField(this)">
                    <i class="fas fa-minus"></i>
                </button>
            `;
            document.getElementById('memberFields').appendChild(field);
        }
        
        if (memberCount >= maxMembers) {
            document.getElementById('addMemberBtn').style.display = 'none';
        }
    }

    function removeMemberField(button) {
        button.parentElement.remove();
        memberCount--;
        document.getElementById('addMemberBtn').style.display = 'block';
    }

    // Add onblur event to the initial email field
    document.addEventListener('DOMContentLoaded', function() {
        const initialEmailField = document.querySelector('input[name="members[]"]');
        initialEmailField.addEventListener('blur', function() {
            validateEmail(this);
        });
    });

    // Validate email and check if user exists
    function validateEmail(input) {
        // Remove previous validation styling
        input.classList.remove('is-invalid', 'is-valid');
        const errorContainer = input.parentNode.querySelector('.invalid-feedback');
        if (errorContainer) {
            errorContainer.remove();
        }
        const successContainer = input.parentNode.querySelector('.valid-feedback');
        if (successContainer) {
            successContainer.remove();
        }
        
        // Skip empty inputs (they're optional)
        if (input.value.trim() === '') {
            return;
        }
        
        // Check if user is trying to invite themselves
        if (input.value.trim().toLowerCase() === currentUserEmail.toLowerCase()) {
            showError(input, 'Você não pode convidar a si mesmo para o grupo.');
            return;
        }
        
        // Simple email validation
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(input.value)) {
            showError(input, 'Por favor, insira um email válido.');
            return;
        }

        // Check if user exists via AJAX
        fetch(`/api/check-user?email=${encodeURIComponent(input.value.trim())}`)
            .then(response => response.json())
            .then(data => {
                if (!data.exists) {
                    showError(input, 'Este usuário não existe no sistema.');
                } else if (data.inGroup) {
                    showError(input, 'Este usuário já é membro do grupo.');
                } else {
                    showSuccess(input, 'Usuário encontrado! Um convite será enviado.');
                }
            })
            .catch(error => {
                console.error('Error checking user:', error);
            });
    }

    function showError(input, message) {
        input.classList.add('is-invalid');
        const errorMsg = document.createElement('div');
        errorMsg.className = 'invalid-feedback';
        errorMsg.textContent = message;
        input.parentNode.insertBefore(errorMsg, input.nextSibling);
    }

    function showSuccess(input, message) {
        input.classList.add('is-valid');
        const successMsg = document.createElement('div');
        successMsg.className = 'valid-feedback';
        successMsg.textContent = message;
        input.parentNode.insertBefore(successMsg, input.nextSibling);
    }

    // Add form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const emailInputs = document.querySelectorAll('input[name="members[]"]');
        let hasInvalidEmail = false;
        
        emailInputs.forEach(input => {
            // Remove previous validation styling
            input.classList.remove('is-invalid');
            const errorContainer = input.parentNode.querySelector('.invalid-feedback');
            if (errorContainer) {
                errorContainer.remove();
            }
            
            // Skip empty inputs (they're optional)
            if (input.value.trim() === '') {
                return;
            }
            
            // Check if user is trying to invite themselves
            if (input.value.trim().toLowerCase() === currentUserEmail.toLowerCase()) {
                showError(input, 'Você não pode convidar a si mesmo para o grupo.');
                hasInvalidEmail = true;
                return;
            }
            
            // Simple email validation
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(input.value)) {
                showError(input, 'Por favor, insira um email válido.');
                hasInvalidEmail = true;
            }
        });
        
        if (hasInvalidEmail) {
            e.preventDefault();
        }
    });
</script>
@endpush
@endsection

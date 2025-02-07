@extends('layouts.app')

@section('content')

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
                            <label class="form-label">Adicionar Membros (até 3)</label>
                            <div id="memberFields">
                                <div class="input-group mb-2">
                                    <input type="email" name="members[]" class="form-control" 
                                        placeholder="email@exemplo.com">
                                    <button type="button" class="btn btn-success add-member" 
                                        onclick="addMemberField()" id="addMemberBtn">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted">
                                Digite o email dos membros que você deseja adicionar ao grupo
                            </small>
                        </div>

                        <div class="d-grid gap-2">
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

    function addMemberField() {
        if (memberCount < maxMembers) {
            memberCount++;
            const field = document.createElement('div');
            field.className = 'input-group mb-2';
            field.innerHTML = `
                <input type="email" name="members[]" class="form-control" placeholder="email@exemplo.com">
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
</script>
@endpush
@endsection

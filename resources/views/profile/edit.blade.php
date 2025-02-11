@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/profile-style.css') }}">

<div class="container mb-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header rounded-0 bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white">Meu Perfil</h5>
                    <a href="{{ route('dashboard') }}" class="btn btn-light btn-sm">
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
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-4 text-center mb-4">
                                <div class="avatar-wrapper">
                                    <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/default-avatar.png') }}" 
                                         alt="Avatar" class="rounded-circle img-fluid mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                                    <div class="mt-2">
                                        <label for="avatar" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-camera me-2"></i>Alterar Foto
                                        </label>
                                        <input type="file" name="avatar" id="avatar" class="d-none">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label for="name">Nome</label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="email">E-mail</label>
                                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                                           value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="phone">Telefone</label>
                                    <input type="tel" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" 
                                           value="{{ old('phone', $user->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="position">Cargo/Posição</label>
                                    <input type="text" name="position" id="position" class="form-control @error('position') is-invalid @enderror" 
                                           value="{{ old('position', $user->position) }}">
                                    @error('position')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="bio">Biografia</label>
                            <textarea name="bio" id="bio" rows="3" class="form-control @error('bio') is-invalid @enderror">{{ old('bio', $user->bio) }}</textarea>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="timezone">Fuso Horário</label>
                            <select name="timezone" id="timezone" class="form-control @error('timezone') is-invalid @enderror">
                                @foreach(timezone_identifiers_list() as $timezone)
                                    <option value="{{ $timezone }}" {{ old('timezone', $user->timezone) == $timezone ? 'selected' : '' }}>
                                        {{ $timezone }}
                                    </option>
                                @endforeach
                            </select>
                            @error('timezone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-wrapper {
    position: relative;
    display: inline-block;
}

.custom-control-input {
    position: absolute;
    left: 0;
    z-index: -1;
    width: 1rem;
    height: 1.25rem;
    opacity: 0;
}

.custom-control-label {
    position: relative;
    margin-bottom: 0;
    vertical-align: top;
    padding-left: 2.5rem;
    cursor: pointer;
}

.custom-control-label::before {
    position: absolute;
    top: 0.25rem;
    left: 0;
    display: block;
    width: 2rem;
    height: 1.25rem;
    content: "";
    background-color: #fff;
    border: 1px solid #adb5bd;
    border-radius: 0.625rem;
    transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.custom-control-label::after {
    position: absolute;
    top: 0.35rem;
    left: 0.1rem;
    display: block;
    width: 1rem;
    height: 1rem;
    content: "";
    background-color: #adb5bd;
    border-radius: 0.625rem;
    transition: transform 0.15s ease-in-out, background-color 0.15s ease-in-out;
}

.custom-control-input:checked ~ .custom-control-label::before {
    color: #fff;
    border-color: #0d6efd;
    background-color: #0d6efd;
}

.custom-control-input:checked ~ .custom-control-label::after {
    background-color: #fff;
    transform: translateX(0.9rem);
}

.custom-control-input:focus ~ .custom-control-label::before {
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}
</style>

<script>
document.getElementById('avatar').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.querySelector('.avatar-wrapper img').src = e.target.result;
        };
        reader.readAsDataURL(e.target.files[0]);
    }
});
</script>
@endsection

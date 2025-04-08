@extends('layouts.auth')

@section('content')


<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">

<div class="auth-wrapper">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h4>Bem vindo á família!</h4>
            </div>

            <div class="auth-body">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="form-group">
                        <label for="name">
                            <i class="fas fa-user"></i>Nome
                        </label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}" 
                               required 
                               autocomplete="name" 
                               autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope"></i>Email
                        </label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autocomplete="email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock"></i>Senha
                        </label>
                        <div class="password-container">
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   autocomplete="new-password"
                                   oninput="checkPasswordStrength(this.value)">
                            <span class="password-toggle" onclick="togglePasswordVisibility('password')">
                                <i class="fas fa-eye" id="togglePassword"></i>
                            </span>
                        </div>
                        <div class="password-strength-container">
                            <div class="password-strength-bar">
                                <div id="password-strength-meter" class="password-strength-meter"></div>
                            </div>
                            <div class="password-requirements">
                                <div id="length-check" class="requirement-item">
                                    <i class="fas fa-times-circle"></i> 8+ caracteres
                                </div>
                                <div id="uppercase-check" class="requirement-item">
                                    <i class="fas fa-times-circle"></i> Letra maiúscula
                                </div>
                                <div id="number-check" class="requirement-item">
                                    <i class="fas fa-times-circle"></i> Número
                                </div>
                                <div id="symbol-check" class="requirement-item">
                                    <i class="fas fa-times-circle"></i> Símbolo
                                </div>
                            </div>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group last-group">
                        <label for="password_confirmation">
                            <i class="fas fa-lock"></i>Confirmar Senha
                        </label>
                        <div class="password-container">
                            <input type="password" 
                                   class="form-control @error('password_confirmation') is-invalid @enderror" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required 
                                   autocomplete="new-password">
                            <span class="password-toggle" onclick="togglePasswordVisibility('password_confirmation')">
                                <i class="fas fa-eye" id="toggleConfirmPassword"></i>
                            </span>
                        </div>
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-register">Registrar</button>
                    </div>

                    <div class="auth-footer">
                        <p>Já tem uma conta? <a href="{{ route('login') }}">Faça login</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility(fieldId) {
    const passwordInput = document.getElementById(fieldId);
    const toggleIcon = document.getElementById(fieldId === 'password' ? 'togglePassword' : 'toggleConfirmPassword');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

function checkPasswordStrength(password) {
    const passwordStrengthMeter = document.getElementById('password-strength-meter');
    const lengthCheck = document.getElementById('length-check');
    const uppercaseCheck = document.getElementById('uppercase-check');
    const numberCheck = document.getElementById('number-check');
    const symbolCheck = document.getElementById('symbol-check');

    let strength = 0;

    if (password.length >= 8) {
        strength += 1;
        lengthCheck.innerHTML = '<i class="fas fa-check-circle"></i> 8+ caracteres';
    } else {
        lengthCheck.innerHTML = '<i class="fas fa-times-circle"></i> 8+ caracteres';
    }

    if (/[A-Z]/.test(password)) {
        strength += 1;
        uppercaseCheck.innerHTML = '<i class="fas fa-check-circle"></i> Letra maiúscula';
    } else {
        uppercaseCheck.innerHTML = '<i class="fas fa-times-circle"></i> Letra maiúscula';
    }

    if (/[0-9]/.test(password)) {
        strength += 1;
        numberCheck.innerHTML = '<i class="fas fa-check-circle"></i> Número';
    } else {
        numberCheck.innerHTML = '<i class="fas fa-times-circle"></i> Número';
    }

    if (/[^A-Za-z0-9]/.test(password)) {
        strength += 1;
        symbolCheck.innerHTML = '<i class="fas fa-check-circle"></i> Símbolo';
    } else {
        symbolCheck.innerHTML = '<i class="fas fa-times-circle"></i> Símbolo';
    }

    switch (strength) {
        case 0:
            passwordStrengthMeter.style.width = '0%';
            passwordStrengthMeter.style.background = 'red';
            break;
        case 1:
            passwordStrengthMeter.style.width = '25%';
            passwordStrengthMeter.style.background = 'orange';
            break;
        case 2:
            passwordStrengthMeter.style.width = '50%';
            passwordStrengthMeter.style.background = 'yellow';
            break;
        case 3:
            passwordStrengthMeter.style.width = '75%';
            passwordStrengthMeter.style.background = 'green';
            break;
        case 4:
            passwordStrengthMeter.style.width = '100%';
            passwordStrengthMeter.style.background = 'green';
            break;
    }
}
</script>
@endsection
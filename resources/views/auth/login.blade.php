@extends('layouts.auth')

@section('content')

<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
<link rel="stylesheet" href="{{ asset('css/style.css') }}">

<div class="auth-wrapper">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h4>{{ env('APP_NAME') }}</h4>
            </div>

            <div class="auth-body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

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
                            autocomplete="email" 
                            autofocus>
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
                                autocomplete="current-password">
                            <span class="password-toggle" onclick="togglePasswordVisibility()">
                                <i class="fas fa-eye" id="togglePassword"></i>
                            </span>
                            <label class="password-reset" for="password-reset">Esqueceu sua senha?</label>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">Lembrar-me</label>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-login">Entrar</button>
                        
                        @if (Route::has('password.request'))
                        <div class="forgot-password">
                            <a href="{{ route('password.request') }}">
                                Esqueceu sua senha?
                            </a>
                        </div>
                        @endif
                    </div>

                    <div class="auth-footer">
                        <p>Não tem uma conta? <a href="{{ route('register') }}">Registre-se</a></p>
                    </div>
                </form>
            </div>
        </div>
        <div class="portfolio">
            <a href="{{ route('portfolio') }}" class="portfolio-anchor mt-5 text-center">Conheça mais do projeto</a>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('togglePassword');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
@endsection
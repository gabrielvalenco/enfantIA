<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <title>Página não encontrada - {{ env('APP_NAME') }}</title>
    <!-- Importação da fonte Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Estilos principais do sistema -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            padding: 0 2rem;
            text-align: center;
        }
        .error-container {
            background-color: var(--surface-color);
            border-radius: 0.75rem;
            box-shadow: var(--card-shadow);
            padding: 2.5rem;
            max-width: 32rem;
            width: 100%;
        }
        .error-code {
            font-size: 6rem;
            font-weight: 700;
            color: var(--link-color);
            line-height: 1;
            margin: 0 0 1rem;
        }
        .error-title {
            font-size: 1.875rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
        }
        .error-message {
            font-size: 1rem;
            margin-bottom: 2rem;
            line-height: 1.6;
            color: var(--text-secondary);
        }
        .error-actions {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            width: 100%;
        }
        .error-actions a {
            padding: 0.75rem 1.5rem;
            border-radius: 2rem;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .primary-action {
            background-color: var(--link-color);
            color: white;
        }
        .primary-action:hover {
            background-color: var(--link-color-hover);
            transform: translateY(-2px);
        }
        .secondary-action {
            border: 2px solid var(--text-secondary);
            color: var(--text-secondary);
            background-color: transparent;
        }
        .secondary-action:hover {
            color: var(--text-secondary-hover);
            border-color: var(--text-secondary-hover);
            transform: translateY(-2px);
        }
        .error-illustration {
            max-width: 200px;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">404</div>
        <h1 class="error-title">Página não encontrada</h1>
        <p class="error-message">
            <i class="fas fa-exclamation-circle"></i> A página que você está procurando não existe ou pode ter sido removida.
        </p>
        <div class="error-actions">
            @auth
                <a href="{{ route('dashboard') }}" class="primary-action">Voltar para o Dashboard</a>
            @else
                <a href="{{ url('/') }}" class="primary-action">Voltar para a Página Inicial</a>
                <a href="{{ route('login') }}" class="secondary-action">Fazer Login</a>
            @endauth
        </div>
    </div>
</body>
</html>

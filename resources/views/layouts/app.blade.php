<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistema de Gerenciamento de Tarefas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/custom-styles.css') }}">
</head>
<body class="bg-light">
    @yield('content')

    @if(!isset($hideFooter) || !$hideFooter)
    <footer class="footer">
        <span class="footer-name">Desenvolvido por Gabriel de Souza Valenço - 2025</span>
        <div class="social-icons">
            <a href="https://www.linkedin.com/in/gabriel-valen%C3%A7o-480b43276/" target="_blank" rel="noopener noreferrer" class="social-icon linkedin" title="LinkedIn">
                <i class="fab fa-linkedin"></i>
            </a>
            <a href="https://github.com/GabrielValenco" target="_blank" rel="noopener noreferrer" class="social-icon github" title="GitHub">
                <i class="fab fa-github"></i>
            </a>
            <a href="https://www.instagram.com/gabriel.valenco.7" target="_blank" rel="noopener noreferrer" class="social-icon instagram" title="Instagram">
                <i class="fab fa-instagram"></i>
            </a>
        </div>
    </footer>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

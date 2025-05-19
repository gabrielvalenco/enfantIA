<link rel="stylesheet" href="{{ asset('css/footer.css') }}">

<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section about">
                <h3 class="footer-title">{{ env('APP_NAME') }}</h3>
                <p class="footer-description">Sistema de gerenciamento de tarefas profissional para aumentar sua produtividade e organização.</p>
                <div class="contact">
                    <p><i class="fas fa-envelope"></i> contato@enfantia.com</p>
                    <p><i class="fas fa-phone"></i> +55 18 99999-9999</p>
                </div>
            </div>
            <div class="footer-section terms">
                <h3 class="footer-title">Termos e Politicas</h3>
                <ul>
                    <li><a href="{{ route('terms') }}">Termos de Uso</a></li>
                    <li><a href="{{ route('politic') }}">Privacidade</a></li>
                </ul>
            </div>
            <div class="footer-section links">
                <h3 class="footer-title">Links Rápidos</h3>
                <ul>
                    <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('tasks.index') }}">Tarefas</a></li>
                    <li><a href="{{ route('categories.index') }}">Categorias</a></li>
                    <li><a href="{{ route('groups.index') }}">Grupos</a></li>
                </ul>
            </div>
            <div class="footer-section contact-form">
                <h3 class="footer-title">Redes Sociais</h3>
                <div class="socials">
                    <a href="https://github.com/gabrielvalenco/TaskNest" class="social-icon"><i class="fab fa-github"></i></a>
                    <a href="https://www.instagram.com/gabss_valenco/" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.linkedin.com/in/gabriel-valenço-480b43276" class="social-icon"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; {{ date('Y') }} {{ env('APP_NAME') }} | Todos os direitos reservados</p>
    </div>
</footer>
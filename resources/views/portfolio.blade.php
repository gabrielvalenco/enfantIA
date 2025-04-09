<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('APP_NAME') }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('css/portfolio-style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<header>
    <nav>
        <div class="logo">
            <a href="{{ route('dashboard') }}">{{ env('APP_NAME') }}</a>
        </div>
        <div class="nav-links">
            <a href="#about">Sobre</a>
            <a href="#services">Serviços</a>
            <a href="#contact">Contato</a>
            <a href="{{ route('login') }}">Login</a>
        </div>
    </nav>
</header>
<main>
    <div class="hero">
        <img src="{{ asset('images/pexels-pixabay-414659.jpg') }}" alt="hero">
        <div class="hero-text">
            <h1>TaskNest</h1>
            <div class="typing-container">
                <p class="typing-animation"></p>
            </div>
        </div>
    </div>
    <section class="about" id="about">
        <h2>Sobre</h2>
        <div class="about-content">
            <h3>Lorem</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptatum aut, assumenda explicabo quisquam fuga facere debitis itaque tempora nihil provident consectetur, ratione aspernatur vitae suscipit quas temporibus nemo! Quod, ducimus!</p>
        </div>
        <div class="about-content">
            <h3>Lorem</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptatum aut, assumenda explicabo quisquam fuga facere debitis itaque tempora nihil provident consectetur, ratione aspernatur vitae suscipit quas temporibus nemo! Quod, ducimus!</p>
        </div>
        <div class="about-content">
            <h3>Lorem</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptatum aut, assumenda explicabo quisquam fuga facere debitis itaque tempora nihil provident consectetur, ratione aspernatur vitae suscipit quas temporibus nemo! Quod, ducimus!</p>
        </div>
    </section>
    <section class="services" id="services">
        <h2>Serviços</h2>
        <div class="services-container">
            <div class="services-item">
                <img src="{{ asset('images/pexels-pixabay-414659.jpg') }}" alt="service">
            </div>
            <div class="services-item">
                <img src="{{ asset('images/pexels-pixabay-414659.jpg') }}" alt="service">
            </div>
            <div class="services-item">
                <img src="{{ asset('images/pexels-pixabay-414659.jpg') }}" alt="service">
            </div>
        </div>
    </section>

    <!-- divisor aqui -->

    <section class="contact" id="contact">
        <h2>Contato</h2>
        <p></p>
    </section>

    @include('layouts.footer')


    <button id="theme-toggle" class="theme-toggle" aria-label="Toggle dark/light mode">
        <i class="fas fa-moon"></i>
    </button>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get the theme toggle button
            const themeToggle = document.getElementById('theme-toggle');
            const themeIcon = themeToggle.querySelector('i');
            
            // Check if user previously set a theme preference
            const currentTheme = localStorage.getItem('theme') || 'dark';
            
            // Apply the saved theme or default to dark
            document.documentElement.setAttribute('data-theme', currentTheme);
            updateThemeIcon(currentTheme);
            
            // Toggle theme when button is clicked
            themeToggle.addEventListener('click', function() {
                // Get current theme
                const currentTheme = document.documentElement.getAttribute('data-theme');
                
                // Switch to the opposite theme
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                
                // Update the theme
                document.documentElement.setAttribute('data-theme', newTheme);
                
                // Save the theme preference
                localStorage.setItem('theme', newTheme);
                
                // Update the icon
                updateThemeIcon(newTheme);
            });
            
            // Function to update the theme icon
            function updateThemeIcon(theme) {
                if (theme === 'dark') {
                    themeIcon.className = 'fas fa-sun';
                } else {
                    themeIcon.className = 'fas fa-moon';
                }
            }
        });
    </script>

</main>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TASKNEST - Portfolio</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('css/portfolio-style.css') }}">
</head>
<body>
<header>
    <nav>
        <div class="header-right">
            <ul>
                <li><a href="#home">Home</a></li>
                <li><a href="#about">Sobre</a></li>
                <li><a href="#services">Serviços</a></li>
                <li><a href="#contact">Contato</a></li>
            </ul>
        </div>
        <div class="header-left">
            <ul>
                <li><a href="{{ route('login') }}">Login</a></li>
                <li><a href="{{ route('register') }}">Registrar-se</a></li>
            </ul>
        </div>
    </nav>
</header>
<main>
    <div class="hero">
        <img src="{{ asset('images/pexels-pixabay-414659.jpg') }}" alt="hero">
        <div class="hero-text">
            <h1>TaskNest</h1>
            <p class="typing-animation"></p>
        </div>
    </div>
    <section class="about" id="about">
        <h2>Sobre</h2>
        <div class="about-content">
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptatum aut, assumenda explicabo quisquam fuga facere debitis itaque tempora nihil provident consectetur, ratione aspernatur vitae suscipit quas temporibus nemo! Quod, ducimus!</p>
        </div>
    </section>
    <section class="services" id="services">
        <h2>Serviços</h2>
        <div class="services-container">
            <div class="services-item">
                <img src="{{ asset('images/pexels-messalaciulla-942872.jpg') }}" alt="service">
            </div>
            <div class="services-item">
                <img src="{{ asset('images/pexels-messalaciulla-942872.jpg') }}" alt="service">
            </div>
            <div class="services-item">
                <img src="{{ asset('images/pexels-messalaciulla-942872.jpg') }}" alt="service">
            </div>
        </div>
    </section>
    <section class="contact" id="contact">
        <h2>Contato</h2>
        <p></p>
    </section>
</main>
</body>
</html>
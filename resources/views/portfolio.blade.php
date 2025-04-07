@extends('layouts.app')

@section('content')

<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<link rel="stylesheet" href="{{ asset('css/portfolio-style.css') }}">

<div class="navbar bg-primary">
    <ul>
        <li><a href="#">Sobre o projeto</a></li>
        <li><a href="#">Sobre a equipe</a></li>
        <li><a href="#">Contato</a></li>
        <li><a href="{{ route('login') }}">Voltar ao login</a></li>
    </ul>
    <div class="menu-icon">
        &#9776;
    </div>
</div>

<div class="overlay"></div>

<div class="portfolio-header">
    <h1>TASKNEST</h1>
    <div class="portfolio-slogan">
        <h3>
            <span>Planejamento</span>
            <span>Estruturação</span>
            <span>Progresso</span>
            <span>Sucesso</span>
        </h3>
    </div>
</div>

<div class="overlay"></div>

<div class="portfolio-services">
    <h2>Ferramentas</h2>
    <div class="portfolio-services-grid">
        <div class="service-card">
            <img src="#" alt="">
        </div>
        <div class="service-card">
            <img src="#" alt="">
        </div>
        <div class="service-card">
            <img src="#" alt="">
        </div>
    </div>
</div>

<div class="">
    
</div>


<!-- remover depois -->
<br><br><br><br><br>

<script src="{{ asset('js/portfolio.js') }}"></script>

@endsection

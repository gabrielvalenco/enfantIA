<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profile-style.css') }}">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <title>Perfil</title>
</head>
<body>

    <div class="container">
        <div class="table-header">
            <h1>Perfil</h1>
            <div class="table-actions">
                <a class="back-button" href="{{ route('dashboard') }}">
                    <span class="back-text">Voltar ao Dashboard</span>
                    <i class="fas fa-sign-out-alt mobile-icon"></i>
                </a>
                <a href="{{ route('profile.edit') }}" class="edit-button">
                    <span class="add-button">Editar Perfil</span>
                    <!-- <i class="fas fa-edit mobile-icon"></i> -->
                </a>
            </div>
        </div>

        <div class="form-container">
            <!-- Left Content - Profile Info -->
            <div class="left-content">
                <div class="profile-info">
                        <div class="profile-image">
                            <!-- <img src="#" alt="Avatar"> -->
                        </div>
                        <div class="profile-details">
                            <div class="profile-item">
                                <h3>{{ $user->name }}</h3>
                                <p class="profile-text">{{ $user->email }}</p>
                            </div>
                            
                            @if($user->phone)
                            <div class="profile-item">
                                <h3>Telefone</h3>
                                <p class="profile-text">{{ $user->phone }}</p>
                            </div>
                            @endif
                            
                            @if($user->position)
                            <div class="profile-item">
                                <h3>Cargo</h3>
                                <p class="profile-text">{{ $user->position }}</p>
                            </div>
                            @endif
                            
                            @if($user->bio)
                            <div class="profile-item">
                                <h3>Biografia</h3>
                                <p class="profile-text">{{ $user->bio }}</p>
                            </div>
                            @endif
                            
                            <div class="profile-item">
                                <h3>Localidade</h3>
                                <p class="profile-text">{{ $user->locale ?: 'Não informado' }}</p>
                            </div>
                            
                            <div class="profile-item">
                                <h3>Idiomas</h3>
                                <p class="profile-text">{{ $user->languages ?: 'Não informado' }}</p>
                            </div>
                            
                            <div class="social-media-section">
                                <h3>Redes Sociais</h3>
                                <div class="social-media-links">
                                    @if(isset($socialLinks) && count($socialLinks) > 0)
                                        @foreach($socialLinks as $link)
                                            <div class="social-media-item">
                                                <div class="social-icon">
                                                    <i class="@getSocialIcon($link)"></i>
                                                </div>
                                                <a href="{{ $link }}" class="social-link" target="_blank">@getSocialName($link)</a>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="social-media-item">
                                            <div class="social-icon">
                                                <i class="fab fa-linkedin"></i>
                                            </div>
                                            <a href="https://linkedin.com/in/username" class="social-link" target="_blank">LinkedIn</a>
                                        </div>
                                        <div class="social-media-item">
                                            <div class="social-icon">
                                                <i class="fab fa-github"></i>
                                            </div>
                                            <a href="https://github.com/username" class="social-link" target="_blank">GitHub</a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            
            <!-- Right Content - Metrics -->
            <div class="right-content">
                <div class="metrics-container">
                    <h2 class="metrics-title">Métricas de Desempenho</h2>
                    
                    <div class="metrics-grid">
                        <div class="metric-card">
                            <div class="metric-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="metric-details">
                                <h3 class="metric-value">42</h3>
                                <p class="metric-label">Tarefas Concluídas</p>
                            </div>
                        </div>
                        
                        <div class="metric-card">
                            <div class="metric-icon">
                                <i class="fas fa-fire"></i>
                            </div>
                            <div class="metric-details">
                                <h3 class="metric-value">7</h3>
                                <p class="metric-label">Maior Sequência</p>
                            </div>
                        </div>
                        
                        <div class="metric-card">
                            <div class="metric-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="metric-details">
                                <h3 class="metric-value">85%</h3>
                                <p class="metric-label">Tarefas no Prazo</p>
                            </div>
                        </div>
                        
                        <div class="metric-card">
                            <div class="metric-icon">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <div class="metric-details">
                                <h3 class="metric-value">12</h3>
                                <p class="metric-label">Projetos Finalizados</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/script.js') }}"></script>
    <script src="{{ asset('js/profile.js') }}"></script>

</body>

<button id="theme-toggle" class="theme-toggle" aria-label="Toggle dark/light mode">
    <i class="fas fa-moon"></i>
</button>

</html>
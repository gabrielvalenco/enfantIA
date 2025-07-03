<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profile-style.css') }}">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <title>Editar Perfil</title>
    <style>
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .profile-header h1 {
            color: var(--text-primary);
            margin: 0;
            font-size: 24px;
        }
        
        .back-link {
            color: var(--link-color);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .back-link:hover {
            color: var(--link-color-hover);
            transform: translateX(-5px);
        }
        
        .profile-content {
            display: flex;
            flex-wrap: wrap;
            gap: 5rem;
        }

        .profile-left,
        .profile-right {
            flex: 1;
            min-width: 300px;
            box-sizing: border-box;
        }
        .form-group input, 
        .form-group textarea, 
        .form-group select {
            width: 100%;
            box-sizing: border-box;
        }

        
        .profile-section {
            margin-bottom: 30px;
        }
        
        .profile-section h2 {
            color: var(--text-primary);
            font-size: 18px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .avatar-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
            background-color: var(--surface-color);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: var(--text-primary);
            font-size: 14px;
        }
        
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            background-color: var(--surface-color);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 8px 12px;
            border-radius: 4px;
            font-family: 'Poppins', sans-serif;
        }
        
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
            outline: none;
            border-color: var(--link-color);
        }
        
        .social-media-links {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .social-media-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .social-icon {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-primary);
        }
        
        .tag-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 1rem;
        }
        
        .tag {
            background-color: var(--link-color);
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .tag .remove-tag {
            cursor: pointer;
        }
        
        .tag-hint {
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 5px;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background-color: var(--link-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--link-color-hover);
        }
        
        .btn-secondary {
            background-color: transparent;
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }
        
        .btn-secondary:hover {
            background-color: var(--surface-color);
            border-color: var(--text-secondary);
        }
        
        small {
            color: var(--text-secondary);
            font-size: 12px;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="profile-header">
            <h1>Editar Perfil</h1>
            <a href="{{ route('profile.index') }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Voltar ao Perfil
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="profile-content">
            <div class="profile-left">
                <div class="profile-section">
                    <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/avatar-placeholder.png') }}" alt="Avatar" class="avatar-img">
                    
                    <h2>Informações Pessoais</h2>
                    
                    <div class="form-group">
                        <label for="name">Nome</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required>
                        <small>Caso o email seja alterado, uma confirmação será enviada para o novo email.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Telefone</label>
                        <input placeholder="Opcional" type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}">
                    </div>
                    
                    <div class="form-group">
                        <label for="position">Cargo</label>
                        <input placeholder="Opcional" type="text" name="position" id="position" value="{{ old('position', $user->position) }}">
                    </div>
                    
                    <div class="form-group">
                        <label for="bio">Biografia</label>
                        <textarea name="bio" id="bio" rows="4">{{ old('bio', $user->bio) }}</textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="avatar">Avatar</label>
                        <input type="file" name="avatar" id="avatar">
                        <small>Nenhum arquivo escolhido</small>
                    </div>
                </div>
                
            </div>
            
            <div class="profile-right">
                <div class="profile-section">
                    <h2>Redes Sociais</h2>
                    <p>Adicione até 3 links de redes sociais.</p>
                    
                    <div class="social-media-links">
                        <div class="social-media-item">
                            <div class="social-icon">
                                <i id="social-icon-0" class="fab fa-github"></i>
                            </div>
                            <input class="social-media-form" type="text" name="social_links[]" id="social-link-0" 
                                value="{{ isset($socialLinks[0]) ? $socialLinks[0] : '' }}" 
                                placeholder="https://github.com/username" 
                                onchange="detectSocialMedia(this, 'social-icon-0')">
                        </div>
                        <div class="social-media-item">
                            <div class="social-icon">
                                <i id="social-icon-1" class="fab fa-github"></i>
                            </div>
                            <input class="social-media-form" type="text" name="social_links[]" id="social-link-1" 
                                value="{{ isset($socialLinks[1]) ? $socialLinks[1] : '' }}" 
                                placeholder="https://github.com/username" 
                                onchange="detectSocialMedia(this, 'social-icon-1')">
                        </div>
                        <div class="social-media-item">
                            <div class="social-icon">
                                <i id="social-icon-2" class="fab fa-linkedin"></i>
                            </div>
                            <input class="social-media-form" type="text" name="social_links[]" id="social-link-2" 
                                value="{{ isset($socialLinks[2]) ? $socialLinks[2] : '' }}" 
                                placeholder="https://linkedin.com/in/username" 
                                onchange="detectSocialMedia(this, 'social-icon-2')">
                        </div>
                    </div>
                </div>
                
                <div class="profile-section">
                    <h2>Preferências</h2>
                    
                    <div class="form-group">
                        <label for="locale">País</label>
                        <input type="text" name="locale" id="locale" value="{{ old('locale', $user->locale) }}">
                    </div>
                    
                    <div class="form-group">
                        <label>Idiomas (até 3)</label>
                        <div id="languages-container" class="languages-container">
                            <div id="language-tags" class="tag-container">
                                @if($user->languages)
                                    @php
                                        $languages = explode(',', $user->languages);
                                    @endphp
                                    @foreach($languages as $language)
                                        @if(trim($language) != '')
                                            <span class="tag">{{ trim($language) }} <span class="remove-tag">×</span></span>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                            <input type="text" id="language-input" placeholder="Digite um idioma e pressione Enter">
                            <input type="hidden" name="languages" id="languages-hidden" value="{{ old('languages', $user->languages) }}">
                            <div class="tag-hint">Digite um idioma, pressione Enter para adicionar, até 3 idiomas.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="action-buttons">
            <button type="submit" class="save-button">Salvar</button>
            <a href="{{ route('profile.index') }}" class="cancel-button">Cancelar</a>
        </div>
    </form>

    <script src="{{ asset('js/script.js') }}"></script>
    <script src="{{ asset('js/profile/script.js') }}"></script>

</body>

<button id="theme-toggle" class="theme-toggle" aria-label="Toggle dark/light mode">
    <i class="fas fa-moon"></i>
</button>

</html>

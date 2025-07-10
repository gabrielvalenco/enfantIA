<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user/profile-style.css') }}">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <title>Editar Perfil</title>
</head>
<body>

    <div class="container">
        <div class="table-header">
            <h1>Editar Perfil</h1>
            <div class="table-actions">
                <a href="{{ route('profile.index') }}" class="back-button">
                    Voltar ao Perfil
                </a>
                <button type="submit" class="add-button">Salvar</button>
            </div>
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
                    <div class="avatar-container">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="avatar-img" id="avatar-preview">
                        @else
                            <div class="avatar-img d-flex justify-content-center align-items-center" id="avatar-preview-container">
                                <i class="fas fa-user theme-icon"></i>
                            </div>
                            <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" alt="" id="avatar-preview" style="display: none;">
                        @endif
                        <div class="avatar-overlay">
                            <i class="fas fa-camera"></i>
                        </div>
                        <input type="file" name="avatar" id="avatar" accept="image/jpeg,image/png,image/webp" style="display: none">
                    </div>
                    <small class="avatar-hint">Clique na imagem para alterar o avatar</small>
                    
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
    </form>

    <script src="{{ asset('js/script.js') }}"></script>
    <script src="{{ asset('js/profile/script.js') }}"></script>

</body>

<button id="theme-toggle" class="theme-toggle" aria-label="Toggle dark/light mode">
    <i class="fas fa-moon"></i>
</button>

</html>

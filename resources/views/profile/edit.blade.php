@extends('layouts.app')

@section('content')
<div class="container">
    <div class="table-header">
        <h1>Editar Perfil</h1>
        <div class="table-actions">
            <a class="back-button" href="{{ route('profile.index') }}">
                <span class="back-text">Voltar ao Perfil</span>
                <i class="fas fa-sign-out-alt mobile-icon"></i>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="form-container">
        @csrf
        @method('PUT')
        
        <div class="left-content">
            <div class="form-section">
                <h2>Informações Pessoais</h2>
                
                <div class="form-group">
                    <label for="name">Nome</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="phone">Telefone</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}">
                    @error('phone')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="position">Cargo</label>
                    <input type="text" name="position" id="position" value="{{ old('position', $user->position) }}">
                    @error('position')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="bio">Biografia</label>
                    <textarea name="bio" id="bio" rows="4">{{ old('bio', $user->bio) }}</textarea>
                    @error('bio')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="avatar">Avatar</label>
                    <input type="file" name="avatar" id="avatar">
                    @error('avatar')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            
            <div class="form-section">
                <h2>Redes Sociais</h2>
                <p class="social-media-hint">Adicione até 3 links de redes sociais</p>
                
                <div class="social-media-links">
                    @for($i = 0; $i < 3; $i++)
                        <div class="social-media-item">
                            <div class="social-icon" id="social-icon-{{ $i+1 }}">
                                @if(isset($socialLinks[$i]))
                                    <i class="{{ App\Helpers\SocialMediaHelper::getSocialIcon($socialLinks[$i]) }}"></i>
                                @endif
                            </div>
                            <input type="text" 
                                class="social-link-input" 
                                id="social-link-{{ $i+1 }}" 
                                name="social_links[]" 
                                placeholder="Cole o link da sua rede social" 
                                value="{{ $socialLinks[$i] ?? '' }}" 
                                onchange="detectSocialMedia(this, 'social-icon-{{ $i+1 }}')">
                        </div>
                    @endfor
                </div>
            </div>
        </div>
        
        <div class="right-content">
            <div class="form-section">
                <h2>Preferências</h2>
                
                <div class="form-group">
                    <label for="locale">Localidade</label>
                    <input type="text" name="locale" id="locale" value="{{ old('locale', $user->locale) }}">
                </div>
                
                <div class="form-group">
                    <label for="languages">Idiomas</label>
                    <input type="text" name="languages" id="languages" value="{{ old('languages', $user->languages) }}">
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                <a href="{{ route('profile.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/profile.js') }}"></script>
@endpush

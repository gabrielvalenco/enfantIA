<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('APP_NAME') }} - Grupos</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('css/group/index.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="table-header">
            <h1>Meus Grupos</h1>
            <div class="table-actions">
                <a class="back-button" href="{{ route('dashboard') }}">
                   <span class="back-text">Voltar ao Dashboard</span>
                    <i class="fas fa-arrow-left mobile-icon"></i>
                </a>
                <a href="{{ route('groups.create') }}" class="add-button">
                    <i class="fas fa-plus-circle"></i> Novo Grupo
                </a>
            </div>
        </div>

        @if($groups->isEmpty())
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <div class="alert-content">
                    Você ainda não participa de nenhum grupo. Crie um novo grupo ou peça para ser adicionado em um existente!
                </div>
            </div>
        @endif
        
        <div class="groups-container">
            @if(!$groups->isEmpty())
                @foreach($groups as $group)
                    <div class="group-card">
                        <a href="{{ route('groups.show', $group) }}" class="group-card-link"></a>
                        <div class="group-header">
                            <h3><i class="fas fa-layer-group mr-2"></i> {{ $group->name }}</h3>
                            <div class="group-actions">
                                @if($group->isAdmin(Auth::user()))
                                    <button type="button" class="btn btn-danger btn-sm action-btn delete-group-btn" data-group-id="{{ $group->id }}" style="z-index: 2;">
                                        <i class="fas fa-trash"></i> Excluir
                                    </button>
                                @else
                                    <button type="button" class="btn btn-danger btn-sm action-btn leave-group-btn" data-group-id="{{ $group->id }}" style="z-index: 2;">
                                        <i class="fas fa-sign-out-alt"></i> Sair
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="group-body">
                            <p>{{ Str::limit($group->description, 100) }}</p>
                            <div class="members-section">
                                <strong><i class="fas fa-user-friends"></i> Membros ({{ $group->members->count() }}):</strong>
                                <div class="members-list">
                                    @foreach($group->members->take(5) as $member)
                                        <span class="member-badge">
                                            {{ $member->name }}
                                            @if($group->isAdmin($member))
                                                <i class="fas fa-crown"></i>
                                            @endif
                                        </span>
                                    @endforeach
                                    @if($group->members->count() > 5)
                                        <span class="member-badge more-members">
                                            +{{ $group->members->count() - 5 }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <script src="{{ asset('js/script.js') }}"></script>
    
    <script>
        // Evitar que o clique nos botões de ação propague para o card inteiro
        document.addEventListener('DOMContentLoaded', function() {
            const actionButtons = document.querySelectorAll('.action-btn');
            
            actionButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            });
        });
    </script>
    
    <button id="theme-toggle" class="theme-toggle" aria-label="Toggle dark/light mode">
        <i class="fas fa-moon"></i>
    </button>
    
</body>
</html>
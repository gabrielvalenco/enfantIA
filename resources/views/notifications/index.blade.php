<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('APP_NAME') }} - Notificações</title>
 
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('css/notifications/index.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</head>
<body>
    <div class="container">
        <div class="table-header">
            <h1>Notificações</h1>
            <div class="table-actions">
                <a href="{{ route('dashboard') }}" class="back-button">
                    <span class="back-text">Voltar ao Dashboard</span>
                    <i class="fas fa-arrow-left mobile-icon"></i>
                </a>
            </div>
        </div>
        
        <div class="notifications-container">
            @if($pendingInvitations->count() > 0)
                @foreach($pendingInvitations as $invitation)
                    <div class="notification-card">
                        <div class="notification-content">
                            <div class="notification-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="notification-text">
                                <h3>Convite para grupo</h3>
                                <p>Você foi convidado para participar do grupo <strong>{{ $invitation->group->name }}</strong> por <strong>{{ $invitation->group->creator->name }}</strong>.</p>
                                <p class="notification-date">{{ $invitation->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="notification-actions">
                            <form action="{{ route('notifications.reject', $invitation) }}" method="POST">
                                @csrf
                                <button type="submit" class="reject-button">
                                    <i class="fas fa-times"></i> Recusar
                                </button>
                            </form>
                            <form action="{{ route('notifications.accept', $invitation) }}" method="POST" class="me-2">
                                @csrf
                                <button type="submit" class="accept-button">
                                    <i class="fas fa-check"></i> Aceitar
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="no-notifications">
                    <i class="fas fa-bell-slash"></i>
                    <p>Você não tem notificações no momento.</p>
                </div>
            @endif
        </div>
    </div>

    <script src="{{ asset('js/script.js') }}"></script>

    <button id="theme-toggle" class="theme-toggle" aria-label="Toggle dark/light mode">
        <i class="fas fa-moon"></i>
    </button>

</body>
</html>
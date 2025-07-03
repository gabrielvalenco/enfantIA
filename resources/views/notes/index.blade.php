<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('APP_NAME') }} - Notas</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notes/note.css') }}">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

</head>
<body>
    
<div class="container">
        <div class="table-header">
            <h1>Notas</h1>
            <div class="table-actions">
                <a class="back-button" href="{{ route('dashboard') }}">
                    <span class="back-text">Voltar ao Dashboard</span>
                    <i class="fas fa-sign-out-alt mobile-icon"></i>
                </a>
                <button class="add-button">
                    <i class="fas fa-plus-circle"></i>
                    Nova Nota
                </button>
            </div>
        </div>


</body>
</html>
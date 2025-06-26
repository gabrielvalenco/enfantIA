<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<link rel="stylesheet" href="{{ asset('css/task/create.css') }}">
<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<title> {{env('APP_NAME')}} - Criar Tarefa</title>
</head>
<body>
<div class="container">
    <div class="table-header">
        <h1>Criar Tarefa</h1>
        <div class="table-actions">
            <a class="back-button" href="{{ route('dashboard') }}">
                Voltar ao Dashboard
                </a>
            </div>
        </div>

        <div class="form-container">

        </div>
    </div>
</body>

<script src="{{ asset('js/script.js') }}"></script>
    
    <button id="theme-toggle" class="theme-toggle" aria-label="Toggle dark/light mode">
        <i class="fas fa-moon"></i>
    </button>

</html>
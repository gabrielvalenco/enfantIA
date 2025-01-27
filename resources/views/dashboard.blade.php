<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/custom-styles.css') }}">
</head>
<body>
    <div class="container mt-5">
        <h1>Dashboard</h1>
        <div class="list-group">
            <a href="{{ route('tasks.create') }}" class="list-group-item list-group-item-action"><i class="fas fa-plus"></i> Criar Tarefa</a>
            <a href="{{ route('tasks.index', ['status' => 'pending']) }}" class="list-group-item list-group-item-action"><i class="fas fa-tasks"></i> Verificar Tarefas Pendentes</a>
            <a href="{{ route('tasks.index', ['status' => 'completed']) }}" class="list-group-item list-group-item-action"><i class="fas fa-check"></i> Conferir Tarefas Já Feitas</a>
            <a href="{{ route('categories.index') }}" class="list-group-item list-group-item-action"><i class="fas fa-tags"></i> Categorias</a>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Dashboard</h1>
        <div class="list-group">
            <a href="{{ route('tasks.create') }}" class="list-group-item list-group-item-action">Criar Tarefa</a>
            <a href="{{ route('tasks.index', ['status' => 'pending']) }}" class="list-group-item list-group-item-action">Verificar Tarefas Pendentes</a>
            <a href="{{ route('tasks.index', ['status' => 'completed']) }}" class="list-group-item list-group-item-action">Conferir Tarefas Já Feitas</a>
            <a href="{{ route('categories.index') }}" class="list-group-item list-group-item-action">Categorias</a>
        </div>
    </div>
</body>
</html>

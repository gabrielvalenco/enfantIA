<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/custom-styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/category-style.css') }}">
</head>
<body>
    <div class="container mt-4">
        <div class="table-header">
            <h1>Categorias</h1>
            <div class="table-actions">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Voltar</a>
                <a href="{{ route('categories.create') }}" class="btn btn-primary">Nova Categoria</a>
            </div>
        </div>
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @if($categories->isEmpty())
                    <tr>
                        <td colspan="3" class="text-center">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle mr-2"></i>
                                Não há categorias cadastradas no momento.
                            </div>
                        </td>
                    </tr>
                @else
                    @foreach($categories as $category)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="color-preview" style="background-color: {{ $category->color }};"></span>
                                {{ $category->name }}
                            </div>
                        </td>
                        <td>{{ $category->description }}</td>
                        <td>
                            <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-warning btn-sm">Editar</a>
                            <form class="d-inline"="d-inline" action="{{ route('categories.destroy', $category->id) }}" method="POST" style="background-color: transparent;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</body>
</html>

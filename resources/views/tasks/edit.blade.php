<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tarefa</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/custom-styles.css') }}">
</head>
<body>
    <div class="container mt-5">
        <h1>Editar Tarefa</h1>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary mb-3">Voltar</a>
        <form action="{{ route('tasks.update', $task->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="title">Título</label>
                <input type="text" name="title" class="form-control" id="title" value="{{ $task->title }}" required>
            </div>
            <div class="form-group">
                <label for="description">Descrição</label>
                <textarea name="description" class="form-control" id="description" required>{{ $task->description }}</textarea>
            </div>
            <div class="form-group">
                <label>Categorias (selecione até 3)</label>
                <div class="categories-container">
                    @foreach($categories as $category)
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" 
                                   name="categories[]" 
                                   value="{{ $category->id }}" 
                                   id="category{{ $category->id }}" 
                                   class="custom-control-input category-checkbox"
                                   {{ $task->categories->contains($category->id) ? 'checked' : '' }}
                                   onchange="validateCategorySelection(this)">
                            <label class="custom-control-label" for="category{{ $category->id }}">
                                {{ $category->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
                <small class="text-muted category-limit-warning" style="display: none;">
                    Limite máximo de 3 categorias atingido
                </small>
            </div>
            <div class="form-group">
                <label for="due_date">Data de Vencimento</label>
                <input type="date" name="due_date" class="form-control" id="due_date" value="{{ $task->due_date }}" required>
            </div>
            <div class="form-group">
                <label for="urgency">Nível de Urgência</label>
                <select name="urgency" id="urgency" class="form-control" required>
                    <option value="none" {{ $task->urgency == 'none' ? 'selected' : '' }}>Nenhuma</option>
                    <option value="low" {{ $task->urgency == 'low' ? 'selected' : '' }}>Baixa</option>
                    <option value="medium" {{ $task->urgency == 'medium' ? 'selected' : '' }}>Média</option>
                    <option value="high" {{ $task->urgency == 'high' ? 'selected' : '' }}>Alta</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Atualizar Tarefa</button>
        </form>
    </div>
    <script>
    function validateCategorySelection(checkbox) {
        const maxCategories = 3;
        const checkedBoxes = document.querySelectorAll('.category-checkbox:checked');
        const warningElement = document.querySelector('.category-limit-warning');
        
        if (checkedBoxes.length > maxCategories) {
            checkbox.checked = false;
            warningElement.style.display = 'block';
        } else {
            warningElement.style.display = 'none';
        }
    }
    </script>
</body>
</html>

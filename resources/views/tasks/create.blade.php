<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Tarefa</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/custom-styles.css') }}">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="table-header">
            <h1>Criar Tarefa</h1>
            <div class="table-actions">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Voltar</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('tasks.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="title">Título</label>
                        <input type="text" name="title" class="form-control" id="title" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Descrição</label>
                        <textarea name="description" class="form-control" id="description" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Categorias (opcional - máximo 3)</label>
                        <div class="categories-container">
                            @foreach($categories as $category)
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" 
                                           name="categories[]" 
                                           value="{{ $category->id }}" 
                                           id="category{{ $category->id }}" 
                                           class="custom-control-input category-checkbox"
                                           onchange="validateCategorySelection(this)">
                                    <label class="custom-control-label" for="category{{ $category->id }}">
                                        {{ $category->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <small class="text-muted category-limit-warning" style="display: none;">
                            <i class="fas fa-exclamation-circle"></i>
                            Limite máximo de 3 categorias atingido
                        </small>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="due_date">
                                    <i class="far fa-calendar-alt"></i>
                                    Data e Hora de Vencimento
                                </label>
                                <input type="datetime-local" name="due_date" class="form-control" id="due_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="urgency">
                                    <i class="fas fa-exclamation-circle"></i>
                                    Nível de Urgência
                                </label>
                                <select name="urgency" id="urgency" class="form-control" required>
                                    <option value="none">Nenhuma</option>
                                    <option value="low">Baixa</option>
                                    <option value="medium">Média</option>
                                    <option value="high">Alta</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Criar Tarefa
                        </button>
                    </div>
                </form>
            </div>
        </div>
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

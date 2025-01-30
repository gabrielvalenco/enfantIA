<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tarefa</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/custom-styles.css') }}">
    <style>
        @media (max-width: 768px) {
            .container { 
                padding: 10px;
                max-width: 100%;
            }
            .form-group { 
                margin-bottom: 1.25rem;
                width: 100%;
            }
            .categories-container {
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
                margin-top: 0.5rem;
                width: 100%;
            }
            .custom-control {
                padding-left: 2rem;
                margin-bottom: 0.5rem;
            }
            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
                padding: 0.75rem;
            }
            select.form-control,
            input[type="datetime-local"].form-control,
            input[type="text"].form-control,
            textarea.form-control {
                width: 100%;
                height: 45px;
            }
            textarea.form-control {
                height: auto;
                min-height: 100px;
            }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="table-header d-flex justify-content-between align-items-center">
            <h1>Editar Tarefa</h1>
            <div class="table-actions">
                <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Voltar
                </a>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body px-4">
                <form action="{{ route('tasks.update', $task->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="title" class="font-weight-bold">Título</label>
                        <input type="text" name="title" class="form-control form-control-lg" id="title" value="{{ old('title', $task->title) }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description" class="font-weight-bold">Descrição</label>
                        <textarea name="description" class="form-control" id="description" rows="4" required>{{ old('description', $task->description) }}</textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="font-weight-bold">Categorias (opcional - máximo 3)</label>
                        <div class="categories-container mt-2">
                            @foreach($categories as $category)
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" 
                                           name="categories[]" 
                                           value="{{ $category->id }}" 
                                           id="category{{ $category->id }}" 
                                           class="custom-control-input category-checkbox"
                                           {{ in_array($category->id, old('categories', $task->categories->pluck('id')->toArray())) ? 'checked' : '' }}
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

                    <div class="form-group">
                        <label for="due_date" class="font-weight-bold">
                            <i class="far fa-calendar-alt"></i>
                            Data e Hora de Vencimento
                        </label>
                        <input type="datetime-local" 
                               name="due_date" 
                               class="form-control" 
                               id="due_date" 
                               value="{{ old('due_date', \Carbon\Carbon::parse($task->due_date)->format('Y-m-d\TH:i')) }}"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="urgency" class="font-weight-bold">
                            <i class="fas fa-exclamation-circle"></i>
                            Nível de Urgência
                        </label>
                        <select name="urgency" id="urgency" class="form-control" required>
                            <option value="none" {{ old('urgency', $task->urgency) == 'none' ? 'selected' : '' }}>Nenhuma</option>
                            <option value="low" {{ old('urgency', $task->urgency) == 'low' ? 'selected' : '' }}>Baixa</option>
                            <option value="medium" {{ old('urgency', $task->urgency) == 'medium' ? 'selected' : '' }}>Média</option>
                            <option value="high" {{ old('urgency', $task->urgency) == 'high' ? 'selected' : '' }}>Alta</option>
                        </select>
                    </div>

                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i>
                            Atualizar Tarefa
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
            setTimeout(() => {
                warningElement.style.display = 'none';
            }, 3000);
        } else {
            warningElement.style.display = 'none';
        }
    }
    </script>
</body>
</html>

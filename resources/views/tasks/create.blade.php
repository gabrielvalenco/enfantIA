<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Tarefa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/task-form.css') }}">
</head>
<body class="bg-light">
    <div class="task-form-container">
        <div class="task-header d-flex justify-content-between align-items-center flex-wrap">
            <h2 class="mb-0">Criar Nova Tarefa</h2>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary back-button">
                <i class="fas fa-arrow-left me-2"></i>Voltar
            </a>
        </div>

        <div class="form-container">
            <form action="{{ route('tasks.store') }}" method="POST" class="needs-validation" novalidate>
                @csrf
                
                <!-- Título -->
                <div class="form-floating mb-3">
                    <input type="text" 
                           class="form-control" 
                           id="title" 
                           name="title" 
                           placeholder="Título da Tarefa"
                           required>
                    <label for="title">Título da Tarefa</label>
                    <div class="invalid-feedback">
                        Por favor, insira um título para a tarefa.
                    </div>
                </div>

                <!-- Descrição -->
                <div class="form-floating mb-3">
                    <textarea class="form-control" 
                              id="description" 
                              name="description" 
                              placeholder="Descrição da Tarefa" 
                              style="height: 100px"
                              required></textarea>
                    <label for="description">Descrição da Tarefa</label>
                    <div class="invalid-feedback">
                        Por favor, forneça uma descrição para a tarefa.
                    </div>
                </div>

                <!-- Data e Urgência -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="datetime-local" 
                                   class="form-control" 
                                   id="due_date" 
                                   name="due_date"
                                   required>
                            <label for="due_date">
                                <i class="far fa-calendar-alt me-2"></i>Data de Vencimento
                            </label>
                            <div class="invalid-feedback">
                                Selecione uma data de vencimento.
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select" 
                                    id="urgency" 
                                    name="urgency"
                                    required>
                                <option value="" selected disabled>Selecione...</option>
                                <option value="none">Nenhuma</option>
                                <option value="low">Baixa</option>
                                <option value="medium">Média</option>
                                <option value="high">Alta</option>
                            </select>
                            <label for="urgency">
                                <i class="fas fa-exclamation-circle me-2"></i>Nível de Urgência
                            </label>
                            <div class="invalid-feedback">
                                Selecione um nível de urgência.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Categorias -->
                <div class="mb-4">
                    <label class="form-label fw-bold mb-2">
                        Categorias (opcional - máximo 3)
                    </label>
                    <div class="category-grid">
                        @foreach($categories as $category)
                            <div class="form-check">
                                <input type="checkbox" 
                                       class="form-check-input category-checkbox" 
                                       id="category{{ $category->id }}"
                                       name="categories[]"
                                       value="{{ $category->id }}"
                                       onchange="validateCategorySelection(this)">
                                <label class="form-check-label" for="category{{ $category->id }}">
                                    {{ $category->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <div class="category-warning text-danger mt-2 d-none">
                        <i class="fas fa-exclamation-circle me-1"></i>
                        Limite máximo de 3 categorias atingido
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary btn-lg submit-button">
                        <i class="fas fa-plus me-2"></i>Criar Tarefa
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validação de categorias
        function validateCategorySelection(checkbox) {
            const maxCategories = 3;
            const checkedBoxes = document.querySelectorAll('.category-checkbox:checked');
            const warningElement = document.querySelector('.category-warning');
            
            if (checkedBoxes.length > maxCategories) {
                checkbox.checked = false;
                warningElement.classList.remove('d-none');
            } else {
                warningElement.classList.add('d-none');
            }
        }

        // Bootstrap Form Validation
        (() => {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>
</html>

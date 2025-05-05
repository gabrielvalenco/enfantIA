<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Tarefa</title>
    
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/task/create.css') }}">
</head>
<body>
    <div class="main-container">
        <div class="task-form-container">
            <div class="task-header d-flex justify-content-between align-items-center flex-wrap">
                <h1>Criar Nova Tarefa</h1>
                <a class="back-button" href="{{ route('dashboard') }}">
                    Voltar ao Dashboard
                </a>
            </div>

            <div class="form-container">
                <form action="{{ route('tasks.store') }}" method="POST" class="needs-validation p-3" novalidate>
                    @csrf
                    
                    @if(request()->has('group_id'))
                        <input type="hidden" name="group_id" value="{{ request('group_id') }}">
                    @endif
                    
                    <!-- Título -->
                    <div class="mb-3">
                        <input type="text" 
                               class="form-control" 
                               id="title" 
                               name="title" 
                               placeholder="Título da Tarefa"
                               required>
                        <div class="invalid-feedback">
                            Por favor, insira um título para a tarefa.
                        </div>
                    </div>

                    <!-- Descrição -->
                    <div class="mb-3">
                        <textarea class="form-control" 
                                  id="description" 
                                  name="description" 
                                  placeholder="Descrição da Tarefa" 
                                  style="height: 100px"
                                  required></textarea>
                        <div class="invalid-feedback">
                            Por favor, forneça uma descrição para a tarefa.
                        </div>
                    </div>

                    <!-- Data e Urgência -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="date-input-container">
                                <label for="due_date" class="date-label">
                                    <i class="far fa-calendar-alt me-2"></i>Data de Vencimento
                                </label>
                                <input type="datetime-local" 
                                       class="form-control" 
                                       id="due_date" 
                                       name="due_date"
                                       required>
                                <div class="invalid-feedback">
                                    Selecione uma data de vencimento.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="date-input-container">
                                <label for="urgency" class="date-label">
                                    <i class="fas fa-exclamation-circle me-2"></i>Nível de Urgência
                                </label>
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
                                <div class="invalid-feedback">
                                    Selecione um nível de urgência.
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(request()->has('group_id'))
                    <!-- Responsável pela Tarefa -->
                    <div class="mb-4">
                        <label for="assigned_to" class="form-label fw-bold mb-2">
                            <i class="fas fa-user-check me-2"></i>Responsável pela Tarefa
                        </label>
                        <select class="form-select" id="assigned_to" name="assigned_to">
                            <option value="">Selecione um responsável (opcional)</option>
                            @php
                                $group = \App\Models\Group::find(request('group_id'));
                                $members = $group ? $group->members : collect([]);
                            @endphp
                            @foreach($members as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <!-- Categorias -->
                    <div class="mb-5">
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
                    
                    <!-- Subtarefas -->
                    <div class="subtask-section">
                        <label class="form-label fw-bold mb-3">
                            Subtarefas (Opcional)
                        </label>
                        <div id="subtasks-container">
                            <!-- Subtarefas serão adicionadas aqui -->
                        </div>
                        <button type="button" id="add-subtask-btn" class="add-subtask-btn">
                            <i class="fas fa-plus-circle me-2"></i>Adicionar
                        </button>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="submit-button">
                            <i class="fas fa-plus me-2"></i>Criar Tarefa
                        </button>
                    </div>
                </form>
            </div>
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

        // Customize date input
        document.addEventListener('DOMContentLoaded', function() {
            const dateInput = document.getElementById('due_date');
            
            // Set default datetime to current time + 1 day
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            tomorrow.setHours(tomorrow.getHours());
            tomorrow.setMinutes(Math.ceil(tomorrow.getMinutes() / 5) * 5); // Round to nearest 5 min
            
            // Format to YYYY-MM-DDThh:mm
            const year = tomorrow.getFullYear();
            const month = String(tomorrow.getMonth() + 1).padStart(2, '0');
            const day = String(tomorrow.getDate()).padStart(2, '0');
            const hours = String(tomorrow.getHours()).padStart(2, '0');
            const minutes = String(tomorrow.getMinutes()).padStart(2, '0');
            
            // Set as default value
            dateInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
        });

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

        // Funcionalidade de Subtarefas
        document.addEventListener('DOMContentLoaded', function() {
            const addSubtaskBtn = document.getElementById('add-subtask-btn');
            const subtasksContainer = document.getElementById('subtasks-container');
            let subtaskCount = 0;
            
            addSubtaskBtn.addEventListener('click', function() {
                addNewSubtask();
            });
            
            function addNewSubtask() {
                subtaskCount++;
                
                const subtaskDiv = document.createElement('div');
                subtaskDiv.className = 'subtask-item mb-3';
                subtaskDiv.dataset.id = subtaskCount;
                
                subtaskDiv.innerHTML = `
                    <div class="subtask-item-container">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="m-0 text-light">Subtarefa #${subtaskCount}</h6>
                            <button type="button" class="remove-subtask">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="mb-3">
                            <input type="text" 
                                   class="form-control" 
                                   id="subtask_title_${subtaskCount}" 
                                   name="subtasks[${subtaskCount}][title]" 
                                   placeholder="Título da Subtarefa"
                                   required>
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" 
                                      id="subtask_description_${subtaskCount}" 
                                      name="subtasks[${subtaskCount}][description]" 
                                      placeholder="Descrição da Subtarefa" 
                                      style="height: 80px"></textarea>
                        </div>
                    </div>
                `;
                
                subtasksContainer.appendChild(subtaskDiv);
                
                // Adicionar evento para remover subtarefa
                const removeBtn = subtaskDiv.querySelector('.remove-subtask');
                removeBtn.addEventListener('click', function() {
                    subtaskDiv.remove();
                });
            }
        });
    </script>
</body>
</html>

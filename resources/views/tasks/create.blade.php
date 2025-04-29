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
                        <button type="submit" class="submit-button">
                            <i class="fas fa-plus me-2"></i>Criar Tarefa
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="subtask-container text-center mt-4">
            <button type="button" id="add-subtask-btn" class="btn">
                <i class="fas fa-tasks me-2"></i>Adicionar Subtarefa
            </button>
            
            <div id="subtasks-section" class="mt-3" style="display: none;">
                <div class="subtask-list container">
                    <!-- Aqui serão adicionadas as subtarefas dinamicamente -->
                </div>
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
            const subtasksSection = document.getElementById('subtasks-section');
            const subtaskList = document.querySelector('.subtask-list');
            let subtaskCount = 0;
            
            addSubtaskBtn.addEventListener('click', function() {
                // Mostrar a seção de subtarefas se estiver oculta
                if (subtasksSection.style.display === 'none') {
                    subtasksSection.style.display = 'block';
                }
                
                // Adicionar nova subtarefa
                addNewSubtask();
            });
            
            function addNewSubtask() {
                subtaskCount++;
                
                const subtaskDiv = document.createElement('div');
                subtaskDiv.className = 'subtask-item card mb-3 shadow-sm';
                subtaskDiv.dataset.id = subtaskCount;
                
                subtaskDiv.innerHTML = `
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title mb-0">Subtarefa #${subtaskCount}</h5>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-subtask">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" 
                                   class="form-control" 
                                   id="subtask_title_${subtaskCount}" 
                                   name="subtasks[${subtaskCount}][title]" 
                                   placeholder="Título da Subtarefa"
                                   required>
                            <label for="subtask_title_${subtaskCount}">Título da Subtarefa</label>
                        </div>
                        <div class="form-floating">
                            <textarea class="form-control" 
                                      id="subtask_description_${subtaskCount}" 
                                      name="subtasks[${subtaskCount}][description]" 
                                      placeholder="Descrição da Subtarefa" 
                                      style="height: 80px"></textarea>
                            <label for="subtask_description_${subtaskCount}">Descrição (opcional)</label>
                        </div>
                    </div>
                `;
                
                subtaskList.appendChild(subtaskDiv);
                
                // Adicionar evento para remover subtarefa
                const removeBtn = subtaskDiv.querySelector('.remove-subtask');
                removeBtn.addEventListener('click', function() {
                    subtaskDiv.remove();
                    
                    // Se não houver mais subtarefas, ocultar a seção
                    if (subtaskList.children.length === 0) {
                        subtasksSection.style.display = 'none';
                    }
                });
            }
        });
    </script>
</body>
</html>

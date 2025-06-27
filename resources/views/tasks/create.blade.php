<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/task/create.css') }}">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <title>{{ env('APP_NAME') }} - Criar Tarefa</title>
</head>
<body>
    <div class="container">
        <div class="table-header">
            <h1>Criar Tarefa</h1>
            <div class="table-actions">
                <a class="back-button" href="{{ route('dashboard') }}">
                    <span class="back-text">Voltar ao Dashboard</span>
                    <i class="fas fa-sign-out-alt mobile-icon"></i>
                </a>
            </div>
        </div>

        <form id="task-form" class="needs-validation" action="{{ route('tasks.store') }}" method="POST" novalidate>
            @csrf

            @if(request()->has('group_id'))
                <input type="hidden" name="group_id" value="{{ request('group_id') }}">
            @endif
            
            <!-- Form content wrapper for responsive layout -->
            <div class="form-container">
                <!-- Left Content -->
                <div class="left-content">
                    <div class="form-group">
                        <h3>Título</h3>
                        <div class="input-wrapper">
                            <input type="text" name="title" id="title" class="form-control" style="width: 100%;" placeholder="Título" maxlength="90" required>
                            <div class="char-count-inside"><span id="title-count">0</span>/90</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <h3>Descrição</h3>
                        <div class="input-wrapper">
                            <textarea name="description" id="description" class="form-control" style="width: 100%;" placeholder="Descrição" maxlength="200" required></textarea>
                            <div class="char-count-inside"><span id="description-count">0</span>/200</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="category-container">
                            <h3>Categorias</h3>
                            <small>Selecione até 3 categorias</small>
                        </div>
                        <div class="category-grid">
                            @foreach($categories as $category)
                                <div class="category-item">
                                    <input 
                                        type="checkbox"
                                        class="category-checkbox"
                                        id="category{{ $category->id }}"
                                        name="categories[]"
                                        value="{{ $category->id }}"
                                        onchange="validateCategorySelection(this)"
                                        hidden>
                                    <label class="category-label" for="category{{ $category->id }}">
                                        <span class="color-preview" style="background-color: {{ $category->color }};"></span>
                                        <span class="category-name">{{ $category->name }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Right Content -->
                <div class="right-content">
                    <div class="sub-itens">
                        <div class="form-group">
                            <h3>Data</h3>
                            <input type="datetime-local" name="due_date" id="due_date" class="form-control" style="width: 100%;" required>
                        </div>

                        <div class="form-group">
                            <h3>Urgência</h3>
                            <select class="form-control" id="urgency" name="urgency" required>
                                <option value="" selected disabled>Selecione...</option>
                                <option value="none">Nenhuma</option>
                                <option value="low">Baixa</option>
                                <option value="medium">Média</option>
                                <option value="high">Alta</option>
                            </select>
                        </div>

                        @if(request()->has('group_id'))
                            <div class="form-group">
                                <h3>Responsável</h3>
                                <select class="form-control" id="assigned_to" name="assigned_to" style="width: 90%;">
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
                    </div>

                    <div class="form-group">
                        <div class="subtasks-header">
                            <button type="button" id="add-subtask-btn" class="add-subtask-btn">
                                <i class="fas fa-plus"></i> Adicionar Subtarefa
                            </button>
                        </div>
                        <div id="subtasks-container" class="subtasks-container">
                            <!-- Subtasks will be added here dynamically -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Submit Button -->
            <div class="submit-container">
                <button type="submit" class="submit-button">Criar Tarefa</button>
            </div>
        </form>
    </div>

    <script src="{{ asset('js/script.js') }}"></script>
    <script src="{{ asset('js/tasks/create.js') }}"></script>

    <button id="theme-toggle" class="theme-toggle" aria-label="Toggle dark/light mode">
        <i class="fas fa-moon"></i>
    </button>
</body>
</html>

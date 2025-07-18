<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Lista de Tarefas' }}</title>

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/task/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/task/actions-modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <!-- Dados das tarefas para uso nos filtros -->
    <script>
        window.taskCategories = [
            @foreach($tasks as $task)
                @if(!$task->status)
                {
                    taskId: {{ $task->id }},
                    categories: [
                        @foreach($task->categories as $category)
                            { id: {{ $category->id }}, name: "{{ $category->name }}" },
                        @endforeach
                    ]
                },
                @endif
            @endforeach
        ];
    </script>
</head>
<body>
    <div class="containerw">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @foreach($tasks as $task)
            @php
                $dueDate = \Carbon\Carbon::parse($task->due_date);
                $now = \Carbon\Carbon::now();
                $hoursUntilDue = $now->diffInHours($dueDate, false);
            @endphp

            @if($hoursUntilDue <= 24 && $hoursUntilDue > 0)
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <div class="alert-body">
                            <strong>Atenção!</strong> A tarefa "<span class="fw-bold">{{ $task->title }}</span>" vence em menos de 24 horas.
                            <br>
                            <small>Data de vencimento: {{ $dueDate->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

        <div class="table-header">
            <h1>{{ $title ?? 'Lista de Tarefas' }}</h1>
            <div class="table-actions">
                @if(isset($group))
                    <a class="back-button" href="{{ route('groups.show', $group->id) }}">
                        Voltar ao Grupo
                    </a>
                    <a class="add-button" href="{{ route('tasks.create', ['group_id' => $group->id]) }}">
                        Nova Tarefa no Grupo
                    </a>
                @else
                    <a class="back-button" href="{{ route('dashboard') }}">
                        Voltar ao Dashboard
                    </a>
                    <a class="add-button" href="{{ route('tasks.create') }}">
                        Nova Tarefa
                    </a>
                @endif
            </div>
        </div>

        <!-- Filtros e pesquisa -->
        <div class="filters-container mb-4">
            <div class="row">
                <div class="col-lg-6 col-md-12 mb-3">
                    <div class="input-group search-box">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" id="task-search" class="form-control" placeholder="Pesquisar tarefa por título...">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="clear-search">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12">
                    <div class="filter-buttons d-flex flex-wrap">
                        <div class="dropdown filter-dropdown mr-2 mb-2">
                            <button class="btn dropdown-toggle filter-button" type="button" id="categoryFilterBtn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-tag mr-1"></i> Categorias
                            </button>
                            <div class="dropdown-menu" aria-labelledby="categoryFilterBtn">
                                <a class="dropdown-item" href="#" data-category="all">Todas</a>
                                <div class="dropdown-divider"></div>
                                @foreach($categories = \App\Models\Category::where('user_id', auth()->id())->get() as $category)
                                    <a class="dropdown-item" href="#" data-category="{{ $category->id }}">{{ $category->name }}</a>
                                @endforeach
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-category="none">Sem categoria</a>
                            </div>
                        </div>
                        <div class="dropdown filter-dropdown mr-2 mb-2">
                            <button class="btn dropdown-toggle filter-button" type="button" id="urgencyFilterBtn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-exclamation-circle mr-1"></i> Urgência
                            </button>
                            <div class="dropdown-menu" aria-labelledby="urgencyFilterBtn">
                                <a class="dropdown-item" href="#" data-urgency="all">Todas</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-urgency="high">Alta</a>
                                <a class="dropdown-item" href="#" data-urgency="medium">Média</a>
                                <a class="dropdown-item" href="#" data-urgency="low">Baixa</a>
                            </div>
                        </div>
                        <div class="dropdown filter-dropdown mb-2">
                            <button class="btn dropdown-toggle filter-button" type="button" id="dateFilterBtn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-sort mr-1"></i> Ordenar
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dateFilterBtn">
                                <a class="dropdown-item" href="#" data-sort="none">Padrão</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-truncate" href="#" data-sort="date-asc">Antiga → Recente</a>
                                <a class="dropdown-item text-truncate" href="#" data-sort="date-desc">Recente → Antiga</a>
                            </div>
                        </div>
                        <!-- Botões de ação em massa -->
                        <div class="multi-action-buttons">
                            <button type="button" class="selectable btn action-button mb-2 mr-2" id="multi-complete-btn" title="Selecionar para concluir" onclick="toggleSelectionMode('complete')">
                                <i class="fas fa-check-circle"></i>
                            </button>
                            <button type="button" class="selectable btn action-button mb-2" id="multi-delete-btn" title="Selecionar para excluir" onclick="toggleSelectionMode('delete')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="active-filters" class="mt-2 d-none">
                <small class="text-muted">Filtros ativos: <span id="filter-tags"></span> <a href="#" id="clear-all-filters" class="ml-2"><i class="fas fa-times-circle"></i> Limpar todos</a></small>
            </div>
        </div>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th id="title-header">Título</th>
                        <th id="description-header">Descrição</th>
                        <th id="categories-header" class="text-center">Categorias</th>
                        <th id="urgency-header">Urgência</th>
                        <th id="due-date-header" class="due-date-header">Data de Vencimento</th>
                        <th id="actions-header" class="actions-column">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @if($tasks->isEmpty())
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Não há tarefas pendentes no momento.
                                </div>
                            </td>
                        </tr>
                    @else
                        @foreach($tasks as $task)
                            @if(!$task->status)
                                <tr class="task-row" 
                                    data-task-id="{{ $task->id }}"
                                    data-category-ids="{{ $task->categories->pluck('id')->join(',') }}"
                                    data-urgency="{{ $task->urgency }}"
                                    data-due-date="{{ $task->due_date }}"
                                    data-original-index="{{ $loop->index }}">
                                    <td class="task-title">{{ $task->title }}</td>
                                    <td class="task-description">{{ $task->description }}</td>
                                    <td class="categories-column">
                                        @if($task->categories->isNotEmpty())
                                            <div class="d-block gap-2">
                                                @foreach($task->categories as $category)
                                                    <div class="category-badge p-2 m-1" style="border-color: {{ $category->color }}; background-color: {{ $category->color }}20;">
                                                        {{ $category->name }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted">Sem categoria</span>
                                        @endif
                                    </td>
                                    <td class="task-urgency">
                                        @switch($task->urgency)
                                            @case('high')
                                                @php
                                                    $dueDate = \Carbon\Carbon::parse($task->due_date);
                                                    $now = \Carbon\Carbon::now();
                                                    $hoursUntilDue = $now->diffInHours($dueDate, false);
                                                @endphp
                                                @if($hoursUntilDue <= 24 && $hoursUntilDue > 0)
                                                    <span class="text-center badge-urgent p-2">
                                                        <i class="fas fa-exclamation-circle"></i>
                                                        Prioridade
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger p-2">Alta</span>
                                                @endif
                                                @break
                                            @case('medium')
                                                <span class="badge bg-warning p-2">Média</span>
                                                @break
                                            @default
                                                <span class="badge bg-info p-2">Baixa</span>
                                        @endswitch
                                    </td>
                                    <td class="due-date">
                                        {{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="actions-column">
                                        <div class="action-buttons">
                                            <button type="button" class="btn btn-sm btn-primary action-btn task-actions-btn" 
                                                data-task-id="{{ $task->id }}" 
                                                data-task-title="{{ $task->title }}"
                                                data-task-description="{{ $task->description }}"
                                                data-task-due-date="{{ $task->due_date }}"
                                                data-task-urgency="{{ $task->urgency }}"
                                                data-task-categories="{{ $task->categories->pluck('id')->join(',') }}"
                                                title="Ações">
                                                <i class="fas fa-cog"></i> Ações
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Indicador de modo de seleção -->
    <div class="selection-mode-active" id="selection-mode-indicator">
        <span class="selection-count">0 selecionada(s)</span>
        <button class="btn btn-confirm" id="confirm-selection" onclick="confirmSelection()">Confirmar</button>
        <button class="btn btn-warning" id="mark-all-btn" onclick="markAllTasks()">Marcar todas</button>
        <button class="btn btn-cancel" id="cancel-selection" onclick="exitSelectionMode()">Cancelar</button>
    </div>
    
    <!-- Overlay para modo de seleção -->
    <div id="selection-overlay" class="selection-overlay"></div>
    
    <!-- Os scripts de seleção de tarefas estão no arquivo index.js -->

    <!-- Modal para Subtarefas -->
    <div class="modal fade" id="subtasksModal" tabindex="-1" aria-labelledby="subtasksModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="subtasksModalLabel">Detalhes da Tarefa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="task-info">
                        <h4 id="task-title"></h4>
                        <p id="task-description"></p>
                        <div class="d-flex flex-column">
                            <div class="mb-3">
                                <strong>Data de Vencimento:</strong> <span id="task-due-date"></span>
                            </div>
                            <div>
                                <strong>Urgência:</strong> <span id="task-urgency"></span>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h5>Subtarefas</h5>
                    <div id="subtasks-list" class="mb-3">
                        <!-- Lista de subtarefas será carregada via AJAX -->
                        <div class="text-center py-3 d-none" id="no-subtasks">
                            <p class="text-muted">Esta tarefa não possui subtarefas.</p>
                        </div>
                    </div>
                    
                    <div id="add-subtask-form">
                        <form id="subtask-form">
                            <div class="form-group">
                                <input type="text" class="form-control" id="subtask-title" placeholder="Nova subtarefa..." required>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary mt-2">Adicionar Subtarefa</button>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" id="complete-task-btn">
                        Concluir Tarefa
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Tarefa -->
    <div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTaskModalLabel">Editar Tarefa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="edit-task-form">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit-task-id">
                        
                        <div class="form-group">
                            <label for="edit-title" class="font-weight-bold">Título</label>
                            <input type="text" name="title" class="form-control form-control-lg" id="edit-title" required>
                        </div>

                        <div class="form-group">
                            <label for="edit-description" class="font-weight-bold">Descrição</label>
                            <textarea name="description" class="form-control" id="edit-description" rows="4" required></textarea>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Categorias (opcional - máximo 3)</label>
                            <div id="edit-categories-container" class="categories-container mt-2">
                                <!-- Categorias carregadas dinamicamente -->
                            </div>
                            <small class="text-muted category-limit-warning" style="display: none;">
                                <i class="fas fa-exclamation-circle"></i>
                                Limite máximo de 3 categorias atingido
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="edit-due-date" class="font-weight-bold">
                                Data e Hora de Vencimento
                            </label>
                            <div class="input-group date-input-group">
                                <input type="datetime-local" 
                                    name="due_date" 
                                    class="form-control" 
                                    id="edit-due-date" 
                                    required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="edit-urgency" class="font-weight-bold">
                                Nível de Urgência
                            </label>
                            <select name="urgency" id="edit-urgency" class="form-control" required>
                                <option value="low">Baixa</option>
                                <option value="medium">Média</option>
                                <option value="high">Alta</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="save-edit-task-btn">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Ações da Tarefa -->
    <div class="modal fade" id="taskActionsModal" tabindex="-1" aria-labelledby="taskActionsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="taskActionsModalLabel">Ações da Tarefa</h5>
                </div>
                <div class="modal-body">
                    <!-- Form content wrapper for responsive layout -->
                    <div class="form-container">
                        <!-- Left Content -->
                        <div class="left-content">
                            <div class="form-group">
                                <h3>Título</h3>
                                <div class="input-wrapper">
                                    <input type="text" id="actions-title" class="form-control" style="width: 100%;" maxlength="90" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <h3>Descrição</h3>
                                <div class="input-wrapper">
                                    <textarea id="actions-description" class="form-control" style="width: 100%;" maxlength="600" required></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="category-container">
                                    <h3>Categorias</h3>
                                    <small>Selecione até 3 categorias</small>
                                </div>
                                <div id="actions-categories-container" class="category-grid">
                                    @foreach($categories = \App\Models\Category::where('user_id', auth()->id())->get() as $category)
                                        <div class="category-item">
                                            <input 
                                                type="checkbox"
                                                class="category-checkbox"
                                                id="action-category{{ $category->id }}"
                                                name="action-categories[]"
                                                value="{{ $category->id }}"
                                                onchange="validateCategorySelection(this)"
                                                hidden>
                                            <label class="category-label" for="action-category{{ $category->id }}">
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
                                    <input type="datetime-local" id="actions-due-date" class="form-control" style="width: 100%;" required>
                                </div>

                                <div class="form-group">
                                    <h3>Urgência</h3>
                                    <select class="form-control custom-select" id="actions-urgency" required>
                                        <option value="low">Baixa</option>
                                        <option value="medium">Média</option>
                                        <option value="high">Alta</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="subtasks-header">
                                    <h3>Subtarefas</h3>
                                    <button type="button" id="actions-add-subtask-btn" class="add-subtask-btn">
                                        <i class="fas fa-plus"></i> Adicionar Subtarefa
                                    </button>
                                </div>
                                <div id="actions-subtasks-container" class="subtasks-container">
                                    <!-- Subtasks will be loaded dynamically -->
                                </div>
                                <div id="new-actions-subtask-form" style="display: none;" class="mb-3 mt-3">
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" id="new-actions-subtask-title" placeholder="Título da subtarefa" maxlength="90">
                                    </div>
                                    <div class="input-group">
                                        <textarea class="form-control" id="new-actions-subtask-description" placeholder="Descrição (opcional)" rows="2"></textarea>
                                    </div>
                                    <div class="d-flex justify-content-end mt-2">
                                        <button type="button" class="btn btn-sm btn-secondary mr-2" id="cancel-actions-subtask-btn">Cancelar</button>
                                        <button type="button" class="btn btn-sm btn-success" id="save-actions-subtask-btn">Adicionar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="actions-task-id">
                </div>
                <div class="modal-footer">
                    <div class="action-buttons-container">
                        <button type="button" class="btn btn-outline-danger" id="delete-actions-task-btn">
                            <i class="fas fa-trash"></i> Excluir Tarefa
                        </button>
                        <button type="button" class="btn btn-primary" id="save-actions-task-btn">
                            <i class="fas fa-save"></i> Salvar Alterações
                        </button>
                        <button type="button" class="btn btn-success" id="complete-actions-task-btn">
                            <i class="fas fa-check-circle"></i> Concluir Tarefa
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/script.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Garantir que o CSRF token esteja disponível para todas as requisições AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    <script src="{{ asset('js/tasks/index.js') }}"></script>

    @stack('scripts')
</body>

<button id="theme-toggle" class="theme-toggle" aria-label="Toggle dark/light mode">
    <i class="fas fa-moon"></i>
</button>
</html>



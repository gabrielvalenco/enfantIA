<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title> {{ env('APP_NAME') }} - Tarefas Concluídas</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('css/task/completed.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="table-header">
            <h1>Tarefas Concluídas</h1>
            <div class="table-actions">
                <a class="back-button" href="{{ route('dashboard') }}">
                   <span class="back-text">Voltar ao Dashboard</span>
                    <i class="fas fa-sign-out-alt mobile-icon"></i>
                </a>
                <button class="delete-task-button" id="clear-tasks-btn">
                    <i class="fas fa-trash delete-mobile-icon"></i> 
                    <span class="back-text">Limpar Tarefas</span>
                </button>
            </div>
        </div>

        <div class="filters-container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="search-box">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" id="task-search" class="form-control" placeholder="Pesquisar tarefa concluída por título...">
                        <div class="input-group-append">
                            <button class="btn-outline-secondary" type="button" id="clear-search">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="filter-buttons">
                        <div class="filter-dropdown">
                            <button class="filter-button dropdown-toggle" type="button" id="categoryFilterBtn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-tag"></i> Categorias
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
                        <div class="filter-dropdown">
                            <button class="filter-button dropdown-toggle" type="button" id="dateFilterBtn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-sort"></i> Ordenar
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dateFilterBtn">
                                <a class="dropdown-item" href="#" data-sort="none">Padrão</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-sort="date-asc">Antiga → Recente</a>
                                <a class="dropdown-item" href="#" data-sort="date-desc">Recente → Antiga</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="task-info-text">
                <small><i class="fas fa-info-circle"></i> As tarefas concluídas são automaticamente excluídas após 1 semana, mas permanecem registradas no seu perfil para estatísticas.</small>
            </div>
            <div id="active-filters" class="d-none">
                <small class="text-muted">Filtros ativos: <span id="filter-tags"></span> <a href="#" id="clear-all-filters" class="ml-2"><i class="fas fa-times-circle"></i> Limpar todos</a></small>
            </div>
        </div>

        <div class="table-responsive m-0">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="title-header">Título</th>
                        <th class="categories-header">Categorias</th>
                        <th class="due-date-header">Data de Conclusão</th>
                        <th class="actions-header">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @if($tasks->isEmpty())
                        <tr>
                            <td colspan="4" class="text-center">
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Não há tarefas concluídas no momento.
                                </div>
                            </td>
                        </tr>
                    @else
                        @foreach($tasks as $task)
                            <tr class="task-row" 
                                data-task-id="{{ $task->id }}"
                                data-category-ids="{{ $task->categories->pluck('id')->join(',') }}"
                                data-completion-date="{{ $task->updated_at->format('Y-m-d H:i:s') }}"
                                data-original-index="{{ $loop->index }}">
                                <td class="task-title">{{ $task->title }}</td>
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
                                <td class="due-date">{{ $task->updated_at->format('d/m/Y H:i') }}</td>
                                <td class="actions-column">
                                    <div class="action-buttons">
                                        <form action="{{ route('tasks.uncomplete', $task) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-warning action-btn" title="Desfazer">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline delete-task-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger action-btn delete-task-btn" data-task-id="{{ $task->id }}" data-task-title="{{ $task->title }}" title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/script.js') }}"></script>
    <script>
        // Define routes for the external script
        var uncompleteRoutes = {
            clearAll: "{{ route('tasks.clear-all') }}"
        };
        
        // Inicializar tooltips do Bootstrap
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
    <script src="{{ asset('js/tasks/completed.js') }}"></script>
</body>

<button id="theme-toggle" class="theme-toggle" aria-label="Toggle dark/light mode">
    <i class="fas fa-moon"></i>
</button>

</html>
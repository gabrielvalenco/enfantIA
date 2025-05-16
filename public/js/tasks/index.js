// Garantir que o código só execute após o DOM e as variáveis globais estarem disponíveis
document.addEventListener('DOMContentLoaded', function() {
    // Desabilitar temporariamente o evento de seleção de texto
    document.body.style.userSelect = 'none';
    
    // Variáveis globais para a seleção
    var selectedTasksIds = [];
    var selectionMode = null;
    
    // Função para entrar no modo de seleção de tarefas
    window.toggleSelectionMode = function(mode) {
        console.log('Ativando modo:', mode);
        
        // Se já estiver neste modo, desativa
        if (selectionMode === mode) {
            console.log('Saindo do modo de seleção');
            exitSelectionMode();
            return;
        }
        
        // Configurar modo e limpar seleções anteriores
        selectionMode = mode;
        selectedTasksIds = [];
        
        // Ativar indicador visual
        document.getElementById('selection-mode-indicator').style.display = 'flex';
        document.getElementById('selection-overlay').style.display = 'block';
        document.body.classList.add('selection-mode-on');
        
        // Destaca o botão ativo
        if (mode === 'complete') {
            document.getElementById('multi-complete-btn').classList.add('active');
            document.getElementById('multi-delete-btn').classList.remove('active');
        } else {
            document.getElementById('multi-delete-btn').classList.add('active');
            document.getElementById('multi-complete-btn').classList.remove('active');
        }
        
        // Desativar botões de ação normais
        var actionButtons = document.querySelectorAll('.action-btn');
        for (var i = 0; i < actionButtons.length; i++) {
            actionButtons[i].style.pointerEvents = 'none';
            actionButtons[i].style.opacity = '0.5';
        }
        
        // Adicionar handlers de clique para selecionar linhas
        var taskRows = document.querySelectorAll('.task-row');
        for (var i = 0; i < taskRows.length; i++) {
            taskRows[i].classList.add('selectable');
            taskRows[i].onclick = function(e) {
                // Ignorar cliques em botões
                if (e.target.closest('button') || e.target.closest('a') || e.target.closest('.action-buttons')) {
                    return;
                }
                
                var row = this;
                var taskId = row.getAttribute('data-task-id');
                
                // Toggle seleção
                if (row.classList.contains('selected')) {
                    row.classList.remove('selected');
                    // Remover da lista
                    var index = selectedTasksIds.indexOf(taskId);
                    if (index !== -1) {
                        selectedTasksIds.splice(index, 1);
                    }
                } else {
                    row.classList.add('selected');
                    // Adicionar à lista
                    if (selectedTasksIds.indexOf(taskId) === -1) {
                        selectedTasksIds.push(taskId);
                    }
                }
                
                // Atualizar contador
                document.querySelector('.selection-count').textContent = selectedTasksIds.length + ' selecionada(s)';
            };
        }
        
        // Resetar contador
        document.querySelector('.selection-count').textContent = '0 selecionada(s)';
    }
    
    // Função para sair do modo de seleção
    window.exitSelectionMode = function() {
        selectionMode = null;
        selectedTasksIds = [];
        
        // Resetar interface
        document.getElementById('selection-mode-indicator').style.display = 'none';
        document.getElementById('selection-overlay').style.display = 'none';
        document.body.classList.remove('selection-mode-on');
        document.getElementById('multi-complete-btn').classList.remove('active');
        document.getElementById('multi-delete-btn').classList.remove('active');
        
        // Limpar seleções visuais
        var selectedRows = document.querySelectorAll('.task-row.selected');
        for (var i = 0; i < selectedRows.length; i++) {
            selectedRows[i].classList.remove('selected');
        }
        
        // Limpar handlers de clique
        var taskRows = document.querySelectorAll('.task-row');
        for (var i = 0; i < taskRows.length; i++) {
            taskRows[i].classList.remove('selectable');
            taskRows[i].onclick = null;
        }
        
        // Reativar botões de ação
        var actionButtons = document.querySelectorAll('.action-btn');
        for (var i = 0; i < actionButtons.length; i++) {
            actionButtons[i].style.pointerEvents = '';
            actionButtons[i].style.opacity = '';
        }
    }
    
    // Função para confirmar a ação selecionada
    window.confirmSelection = function() {
        if (selectedTasksIds.length === 0) {
            Swal.fire({
                title: 'Nenhuma tarefa selecionada',
                text: 'Selecione pelo menos uma tarefa para continuar.',
                icon: 'info',
                background: getComputedStyle(document.documentElement).getPropertyValue('--surface-color'),
                color: getComputedStyle(document.documentElement).getPropertyValue('--text-primary'),
                customClass: {
                    confirmButton: 'swal-confirm-button',
                    title: 'swal-title',
                    htmlContainer: 'swal-html-container'
                }
            });
            return;
        }
        
        if (selectionMode === 'complete') {
            Swal.fire({
                title: 'Concluir tarefas',
                text: 'Deseja marcar ' + selectedTasksIds.length + ' tarefa(s) como concluída(s)?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: 'var(--link-color)',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, concluir!',
                cancelButtonText: 'Cancelar',
                background: getComputedStyle(document.documentElement).getPropertyValue('--surface-color'),
                color: getComputedStyle(document.documentElement).getPropertyValue('--text-primary'),
                customClass: {
                    confirmButton: 'swal-confirm-button',
                    cancelButton: 'swal-cancel-button',
                    title: 'swal-title',
                    htmlContainer: 'swal-html-container'
                }
            }).then(function(result) {
                if (result.isConfirmed) {
                    submitTasks('complete');
                }
            });
        } else if (selectionMode === 'delete') {
            Swal.fire({
                title: 'Excluir tarefas',
                text: 'Deseja excluir ' + selectedTasksIds.length + ' tarefa(s)?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar',
                background: getComputedStyle(document.documentElement).getPropertyValue('--surface-color'),
                color: getComputedStyle(document.documentElement).getPropertyValue('--text-primary'),
                customClass: {
                    confirmButton: 'swal-confirm-button',
                    cancelButton: 'swal-cancel-button',
                    title: 'swal-title',
                    htmlContainer: 'swal-html-container'
                }
            }).then(function(result) {
                if (result.isConfirmed) {
                    submitTasks('delete');
                }
            });
        }
    }
    
    // Função para enviar o formulário com as tarefas selecionadas
    function submitTasks(action) {
        var form = document.createElement('form');
        form.method = 'POST';
        
        if (action === 'complete') {
            form.action = '/tasks/complete-multiple';
        } else {
            form.action = '/tasks/delete-multiple';
        }
        
        // CSRF token
        var token = document.createElement('input');
        token.type = 'hidden';
        token.name = '_token';
        token.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(token);
        
        // IDs das tarefas
        for (var i = 0; i < selectedTasksIds.length; i++) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'task_ids[]';
            input.value = selectedTasksIds[i];
            form.appendChild(input);
        }
        
        document.body.appendChild(form);
        form.submit();
    }
    
    // Manipulador para os botões de completar tarefa
    document.querySelectorAll('.complete-task-btn').forEach(button => {
        button.addEventListener('click', function() {
            const taskId = this.getAttribute('data-task-id');
            const taskRoute = this.getAttribute('data-task-route');
            const buttonElement = this;
            
            // Mostrar confirmação com SweetAlert2
            Swal.fire({
                title: 'Concluir tarefa?',
                text: 'Deseja marcar esta tarefa como concluída?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: 'var(--link-color)',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, concluir!',
                cancelButtonText: 'Cancelar',
                background: getComputedStyle(document.documentElement).getPropertyValue('--surface-color'),
                color: getComputedStyle(document.documentElement).getPropertyValue('--text-primary'),
                customClass: {
                    confirmButton: 'swal-confirm-button',
                    cancelButton: 'swal-cancel-button',
                    title: 'swal-title',
                    htmlContainer: 'swal-html-container'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Adicionar classe para animação
                    buttonElement.classList.add('completed-animation');
                    
                    // Criar e enviar o formulário programaticamente
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = taskRoute;
                    form.style.display = 'none';
                    
                    // Adicionar CSRF token
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    form.appendChild(csrfToken);
                    
                    // Adicionar método PATCH
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'PATCH';
                    form.appendChild(methodField);
                    
                    // Adicionar ao documento e enviar
                    document.body.appendChild(form);
                    
                    // Aguardar um pouco para mostrar a animação antes de enviar
                    setTimeout(() => {
                        form.submit();
                    }, 800);
                }
            });
        });
    });
    
    // Aguardar um pouco para garantir que a variável global taskCategories esteja disponível
    setTimeout(function() {
        $(function() {
            $(document).ready(function() {
                // Inicializar tooltips do Bootstrap
                $('[title]').tooltip({
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body'
                });
                
                // Variáveis para controle de filtros
                let currentTaskId = null;
                let activeFilters = {
                    search: '',
                    category: 'all',
                    urgency: 'all',
                    sort: 'none'
                };
                
                // Verificar se a variável global está disponível
                if (!window.taskCategories) {
                    console.error('Variável taskCategories não encontrada!');
                    window.taskCategories = [];
                }
                
                // ==== FILTROS E PESQUISA ==== 
                
                // Pesquisa por texto
                $('#task-search').on('keyup', function() {
                    activeFilters.search = $(this).val().toLowerCase();
                    applyFilters();
                });
                
                // Limpar pesquisa
                $('#clear-search').on('click', function() {
                    $('#task-search').val('');
                    activeFilters.search = '';
                    applyFilters();
                });
                
                // Filtro por categoria - usando delegação de eventos
                $(document).on('click', '.dropdown-item[data-category]', function(e) {
                    e.preventDefault();
                    const category = $(this).data('category');
                    
                    activeFilters.category = category;
                    
                    $('#categoryFilterBtn').html(`<i class="fas fa-tag mr-1"></i> ${$(this).text()}`);
                    applyFilters();
                    updateActiveFiltersDisplay();
                });
                
                // Filtro por urgência - usando delegação de eventos
                $(document).on('click', '.dropdown-item[data-urgency]', function(e) {
                    e.preventDefault();
                    const urgency = $(this).data('urgency');
                    activeFilters.urgency = urgency;
                    
                    $('#urgencyFilterBtn').html(`<i class="fas fa-exclamation-circle mr-1"></i> ${$(this).text()}`);
                    applyFilters();
                    updateActiveFiltersDisplay();
                });
                
                // Ordenação por data - usando delegação de eventos
                $(document).on('click', '.dropdown-item[data-sort]', function(e) {
                    e.preventDefault();
                    const sort = $(this).data('sort');
                    activeFilters.sort = sort;
                    
                    $('#dateFilterBtn').html(`<i class="fas fa-sort mr-1"></i> ${$(this).text()}`);
                    applyFiltersSorting();
                    updateActiveFiltersDisplay();
                });
                
                // Limpar todos os filtros
                $('#clear-all-filters').on('click', function(e) {
                    e.preventDefault();
                    
                    // Resetar filtros
                    activeFilters = {
                        search: '',
                        category: 'all',
                        urgency: 'all',
                        sort: 'none'
                    };
                    
                    // Resetar UI
                    $('#task-search').val('');
                    $('#categoryFilterBtn').html('<i class="fas fa-tag mr-1"></i> Categorias');
                    $('#urgencyFilterBtn').html('<i class="fas fa-exclamation-circle mr-1"></i> Urgência');
                    $('#dateFilterBtn').html('<i class="fas fa-sort mr-1"></i> Ordenar');
                    
                    // Mostrar todas as tarefas
                    $('.task-row').show();
                    applyFiltersSorting();
                    updateActiveFiltersDisplay();
                });
                
                // Função para atualizar exibição de filtros ativos
                function updateActiveFiltersDisplay() {
                    const filterTags = [];
                    
                    if (activeFilters.category !== 'all') {
                        const categoryText = $(`.dropdown-item[data-category="${activeFilters.category}"]`).text();
                        filterTags.push(`<span class="filter-tag" data-filter="category">Categoria: ${categoryText} <i class="fas fa-times" data-filter="category"></i></span>`);
                    }
                    
                    if (activeFilters.urgency !== 'all') {
                        const urgencyText = $(`.dropdown-item[data-urgency="${activeFilters.urgency}"]`).text();
                        filterTags.push(`<span class="filter-tag" data-filter="urgency">Urgência: ${urgencyText} <i class="fas fa-times" data-filter="urgency"></i></span>`);
                    }
                    
                    if (activeFilters.sort !== 'none') {
                        const sortText = $(`.dropdown-item[data-sort="${activeFilters.sort}"]`).text();
                        filterTags.push(`<span class="filter-tag" data-filter="sort">Ordenar: ${sortText} <i class="fas fa-times" data-filter="sort"></i></span>`);
                    }
                    
                    if (activeFilters.search) {
                        filterTags.push(`<span class="filter-tag" data-filter="search">Pesquisa: "${activeFilters.search}" <i class="fas fa-times" data-filter="search"></i></span>`);
                    }
                    
                    if (filterTags.length > 0) {
                        $('#filter-tags').html(filterTags.join(''));
                        $('#active-filters').removeClass('d-none');
                    } else {
                        $('#active-filters').addClass('d-none');
                    }
                }
                
                // Remover filtro individual
                $(document).on('click', '.filter-tag i', function() {
                    const filterType = $(this).parent().data('filter');
                    
                    switch(filterType) {
                        case 'category':
                            activeFilters.category = 'all';
                            $('#categoryFilterBtn').html('<i class="fas fa-tag mr-1"></i> Categorias');
                            break;
                        case 'urgency':
                            activeFilters.urgency = 'all';
                            $('#urgencyFilterBtn').html('<i class="fas fa-exclamation-circle mr-1"></i> Urgência');
                            break;
                        case 'sort':
                            activeFilters.sort = 'none';
                            $('#dateFilterBtn').html('<i class="fas fa-sort mr-1"></i> Ordenar');
                            break;
                        case 'search':
                            activeFilters.search = '';
                            $('#task-search').val('');
                            break;
                    }
                    
                    applyFilters();
                    updateActiveFiltersDisplay();
                });
                
                // Função para aplicar todos os filtros ativos
                function applyFilters() {
                    // Primeiro limpar qualquer ordenação anterior
                    resetOriginalOrder();
                    
                    // Filtrar por texto, categoria e urgência
                    $('.task-row').each(function() {
                        const $row = $(this);
                        let showRow = true;
                        const taskId = $row.data('task-id');
                        
                        // Filtro de texto
                        if (activeFilters.search) {
                            const title = $row.find('.task-title').text().toLowerCase();
                            const description = $row.find('.task-description').text().toLowerCase();
                            if (!title.includes(activeFilters.search) && !description.includes(activeFilters.search)) {
                                showRow = false;
                            }
                        }
                        
                        // Filtro de categoria
                        if (showRow && activeFilters.category !== 'all') {
                            // Buscar a tarefa em taskCategories (variável global definida em index.blade.php)
                            const taskInfo = window.taskCategories.find(t => t.taskId === taskId);
                            const categoryIds = taskInfo ? taskInfo.categories.map(c => c.id.toString()) : [];
                            
                            if (activeFilters.category === 'none') {
                                // Caso especial: "Sem categoria"
                                if (categoryIds.length > 0) {
                                    showRow = false;
                                }
                            } else {
                                // Verificar se a categoria está na lista
                                const categoryToFind = activeFilters.category.toString();
                                const found = categoryIds.includes(categoryToFind);
                                
                                if (!found) {
                                    showRow = false;
                                }
                            }
                        }
                        
                        // Filtro de urgência
                        if (showRow && activeFilters.urgency !== 'all') {
                            const taskUrgency = $row.data('urgency');
                            if (taskUrgency !== activeFilters.urgency) {
                                showRow = false;
                            }
                        }
                        
                        // Aplicar visibilidade
                        $row.toggle(showRow);
                    });
                    
                    // Então aplicar ordenação se necessário
                    if (activeFilters.sort !== 'none') {
                        applyFiltersSorting();
                    }
                    
                    // Atualizar mensagem de "sem tarefas"
                    updateNoTasksMessage();
                }
                
                // Função para aplicar ordenação
                function applyFiltersSorting() {
                    if (activeFilters.sort === 'none') {
                        resetOriginalOrder();
                        return;
                    }
                    
                    // Função para comparar duas datas
                    const compareDates = (a, b, ascending) => {
                        const dateA = new Date($(a).data('due-date'));
                        const dateB = new Date($(b).data('due-date'));
                        return ascending ? dateA - dateB : dateB - dateA;
                    };
                    
                    const tbody = $('tbody');
                    const rows = $('.task-row').get();
                    
                    // Ordenar linhas
                    rows.sort(function(a, b) {
                        if (activeFilters.sort === 'date-asc') {
                            return compareDates(a, b, true);
                        } else if (activeFilters.sort === 'date-desc') {
                            return compareDates(a, b, false);
                        }
                        return 0;
                    });
                    
                    // Remover e recolocar as linhas na nova ordem
                    $.each(rows, function(index, row) {
                        tbody.append(row);
                    });
                }
                
                // Função para restaurar a ordem original
                function resetOriginalOrder() {
                    const tbody = $('tbody');
                    const rows = $('.task-row').get().sort(function(a, b) {
                        return $(a).data('original-index') - $(b).data('original-index');
                    });
                    
                    $.each(rows, function(index, row) {
                        tbody.append(row);
                    });
                }
                
                // Função para atualizar mensagem de "sem tarefas"
                function updateNoTasksMessage() {
                    const visibleTasks = $('.task-row:visible').length;
                    if (visibleTasks === 0) {
                        // Se não existe, criar mensagem
                        if ($('#no-search-results').length === 0) {
                            $('tbody').append(`
                                <tr id="no-search-results">
                                    <td colspan="7" class="text-center">
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-search mr-2"></i>
                                            Nenhuma tarefa encontrada com os filtros aplicados.
                                        </div>
                                    </td>
                                </tr>
                            `);
                        } else {
                            $('#no-search-results').show();
                        }
                    } else {
                        // Esconder a mensagem se houver tarefas visíveis
                        $('#no-search-results').hide();
                    }
                }
                
                // Atribuir índices originais para manter a ordem inicial
                $('.task-row').each(function(index) {
                    $(this).data('original-index', index);
                });
                
                // ==== MODAL DE TAREFAS E SUBTAREFAS ====
                
                // Remover o comportamento de clique na linha da tabela
                $('.task-row').off('click');
                
                // Abrir modal ao clicar no botão de detalhes da tarefa
                $(document).on('click', '.view-task-btn', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const taskId = $(this).data('task-id');
                    currentTaskId = taskId;
                    
                    // Buscar informações da tarefa e subtarefas
                    $.ajax({
                        url: `/tasks/${taskId}/details`,
                        method: 'GET',
                        success: function(response) {
                            // Preencher informações da tarefa
                            $('#task-title').text(response.task.title);
                            $('#task-description').text(response.task.description);
                            $('#task-due-date').text(response.due_date_formatted);
                            $('#task-urgency').text(response.urgency_formatted);
                            
                            // Preencher subtarefas
                            const subtasksList = $('#subtasks-list');
                            subtasksList.empty();
                            
                            if (response.subtasks.length === 0) {
                                $('#no-subtasks').removeClass('d-none');
                            } else {
                                $('#no-subtasks').addClass('d-none');
                                
                                response.subtasks.forEach(function(subtask) {
                                    subtasksList.append(`
                                        <div class="subtask-item d-flex align-items-center mb-2" data-id="${subtask.id}">
                                            <div class="form-check">
                                                <input class="form-check-input subtask-checkbox" 
                                                       type="checkbox" 
                                                       ${subtask.completed ? 'checked' : ''}
                                                       id="subtask-${subtask.id}">
                                                <label class="form-check-label ${subtask.completed ? 'text-muted text-decoration-line-through' : ''}" 
                                                       for="subtask-${subtask.id}">
                                                    ${subtask.title}
                                                </label>
                                            </div>
                                            <button class="btn btn-sm btn-danger delete-subtask ml-auto">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    `);
                                });
                            }
                            
                            // Mostrar o modal
                            $('#subtasksModal').modal('show');
                        },
                        error: function(xhr, status, error) {
                            console.error('Erro ao carregar dados:', error);
                            Swal.fire({
                                title: 'Erro!',
                                text: 'Não foi possível carregar os detalhes da tarefa.',
                                icon: 'error',
                                confirmButtonText: 'Ok'
                            });
                        }
                    });
                });
                
                // Adicionar nova subtarefa
                $('#subtask-form').on('submit', function(e) {
                    e.preventDefault();
                    
                    const title = $('#subtask-title').val();
                    if (!title) return;
                    
                    $.ajax({
                        url: `/tasks/${currentTaskId}/subtasks`,
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            title: title
                        },
                        success: function(response) {
                            const subtasksList = $('#subtasks-list');
                            $('#no-subtasks').addClass('d-none');
                            
                            subtasksList.append(`
                                <div class="subtask-item d-flex align-items-center mb-2" data-id="${response.subtask.id}">
                                    <div class="form-check">
                                        <input class="form-check-input subtask-checkbox" 
                                               type="checkbox" 
                                               id="subtask-${response.subtask.id}">
                                        <label class="form-check-label" for="subtask-${response.subtask.id}">
                                            ${response.subtask.title}
                                        </label>
                                    </div>
                                    <button class="btn btn-sm btn-danger delete-subtask ml-auto">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            `);
                            
                            $('#subtask-title').val('');
                        }
                    });
                });
                
                // Concluir/desmarcar subtarefa
                $(document).on('change', '.subtask-checkbox', function() {
                    const subtaskId = $(this).closest('.subtask-item').data('id');
                    const checkbox = $(this);
                    const label = checkbox.siblings('label');
                    
                    $.ajax({
                        url: `/subtasks/${subtaskId}/toggle`,
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.completed) {
                                label.addClass('text-muted text-decoration-line-through');
                            } else {
                                label.removeClass('text-muted text-decoration-line-through');
                            }
                        }
                    });
                });
                
                // Excluir subtarefa
                $(document).on('click', '.delete-subtask', function(e) {
                    e.stopPropagation();
                    
                    const subtaskItem = $(this).closest('.subtask-item');
                    const subtaskId = subtaskItem.data('id');
                    
                    Swal.fire({
                        title: 'Tem certeza?',
                        text: 'Você deseja excluir esta subtarefa?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sim, excluir',
                        cancelButtonText: 'Cancelar',
                        background: getComputedStyle(document.documentElement).getPropertyValue('--surface-color'),
                        color: getComputedStyle(document.documentElement).getPropertyValue('--text-primary'),
                        customClass: {
                            confirmButton: 'swal-confirm-button',
                            cancelButton: 'swal-cancel-button',
                            title: 'swal-title',
                            htmlContainer: 'swal-html-container'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `/subtasks/${subtaskId}`,
                                method: 'DELETE',
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function() {
                                    subtaskItem.remove();
                                    
                                    // Mostrar mensagem se não houver mais subtarefas
                                    if ($('#subtasks-list .subtask-item').length === 0) {
                                        $('#no-subtasks').removeClass('d-none');
                                    }
                                    
                                    Swal.fire(
                                        'Excluída!',
                                        'A subtarefa foi excluída com sucesso.',
                                        'success'
                                    );
                                }
                            });
                        }
                    });
                });
                
                // Evitar propagação de cliques no formulário de exclusão e seus elementos
                $(document).on('click', '.delete-task-form, .delete-task-form *', function(e) {
                    e.stopPropagation();
                });
                
                // ==== EDIÇÃO DE TAREFAS EM MODAL ====
                
                // Abrir modal de edição ao clicar no botão de editar
                $(document).on('click', '.edit-task-btn', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const taskId = $(this).data('task-id');
                    
                    // Buscar informações da tarefa para edição
                    $.ajax({
                        url: `/tasks/${taskId}/edit`,
                        method: 'GET',
                        success: function(response) {
                            // Preencher o formulário com os dados da tarefa
                            $('#edit-task-id').val(response.task.id);
                            $('#edit-title').val(response.task.title);
                            $('#edit-description').val(response.task.description);
                            
                            // Converter a data para o formato compatível com datetime-local
                            const dueDate = new Date(response.task.due_date);
                            const formattedDate = dueDate.toISOString().slice(0, 16);
                            $('#edit-due-date').val(formattedDate);
                            
                            // Selecionar a urgência
                            $('#edit-urgency').val(response.task.urgency);
                            
                            // Carregar as categorias
                            const categoriesContainer = $('#edit-categories-container');
                            categoriesContainer.empty();
                            
                            response.categories.forEach(function(category) {
                                const isSelected = response.task_categories.includes(category.id);
                                categoriesContainer.append(`
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" 
                                               name="categories[]" 
                                               value="${category.id}" 
                                               id="edit-category-${category.id}" 
                                               class="custom-control-input edit-category-checkbox"
                                               ${isSelected ? 'checked' : ''}
                                               onchange="validateEditCategorySelection(this)">
                                        <label class="custom-control-label" for="edit-category-${category.id}">
                                            ${category.name}
                                        </label>
                                    </div>
                                `);
                            });
                            
                            // Mostrar o modal
                            $('#editTaskModal').modal('show');
                        },
                        error: function(xhr, status, error) {
                            console.error('Erro ao carregar dados para edição:', error);
                            Swal.fire({
                                title: 'Erro!',
                                text: 'Não foi possível carregar os dados da tarefa para edição.',
                                icon: 'error',
                                confirmButtonText: 'Ok',
                                background: getComputedStyle(document.documentElement).getPropertyValue('--surface-color'),
                                color: getComputedStyle(document.documentElement).getPropertyValue('--text-primary')
                            });
                        }
                    });
                });
                
                // Função para validar a seleção de categorias no modal de edição
                window.validateEditCategorySelection = function(checkbox) {
                    const maxCategories = 3;
                    const checkedBoxes = document.querySelectorAll('.edit-category-checkbox:checked');
                    const warningElement = document.querySelector('#editTaskModal .category-limit-warning');
                    
                    if (checkedBoxes.length > maxCategories) {
                        checkbox.checked = false;
                        warningElement.style.display = 'block';
                        setTimeout(() => {
                            warningElement.style.display = 'none';
                        }, 3000);
                    } else {
                        warningElement.style.display = 'none';
                    }
                };
                
                // Salvar alterações da tarefa
                $('#save-edit-task-btn').on('click', function() {
                    const taskId = $('#edit-task-id').val();
                    
                    // Verificar se o formulário é válido
                    const form = document.getElementById('edit-task-form');
                    if (!form.checkValidity()) {
                        form.reportValidity();
                        return;
                    }
                    
                    // Obter os valores do formulário
                    const title = $('#edit-title').val();
                    const description = $('#edit-description').val();
                    const dueDate = $('#edit-due-date').val();
                    const urgency = $('#edit-urgency').val();
                    
                    // Obter categorias selecionadas
                    const categories = [];
                    $('.edit-category-checkbox:checked').each(function() {
                        categories.push($(this).val());
                    });
                    
                    // Enviar dados para o servidor
                    $.ajax({
                        url: `/tasks/${taskId}`,
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            _method: 'PUT',
                            title: title,
                            description: description,
                            due_date: dueDate,
                            urgency: urgency,
                            categories: categories
                        },
                        success: function(response) {
                            // Fechar o modal
                            $('#editTaskModal').modal('hide');
                            
                            // Exibir mensagem de sucesso
                            Swal.fire({
                                title: 'Sucesso!',
                                text: 'Tarefa atualizada com sucesso!',
                                icon: 'success',
                                confirmButtonText: 'Ok',
                                background: getComputedStyle(document.documentElement).getPropertyValue('--surface-color'),
                                color: getComputedStyle(document.documentElement).getPropertyValue('--text-primary')
                            }).then(() => {
                                // Recarregar a página para mostrar as alterações
                                window.location.reload();
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('Erro ao atualizar tarefa:', error);
                            
                            let errorMessage = 'Erro ao atualizar a tarefa.';
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                            }
                            
                            Swal.fire({
                                title: 'Erro!',
                                html: errorMessage,
                                icon: 'error',
                                confirmButtonText: 'Ok',
                                background: getComputedStyle(document.documentElement).getPropertyValue('--surface-color'),
                                color: getComputedStyle(document.documentElement).getPropertyValue('--text-primary')
                            });
                        }
                    });
                });
                
                // Concluir tarefa
                $('#complete-task-btn').on('click', function() {
                    $.ajax({
                        url: `/tasks/${currentTaskId}/can-complete`,
                        method: 'GET',
                        success: function(response) {
                            if (response.can_complete) {
                                // Usar AJAX com método PATCH em vez de redirecionamento GET
                                $.ajax({
                                    url: `/tasks/${currentTaskId}/complete`,
                                    method: 'POST',
                                    data: {
                                        _token: $('meta[name="csrf-token"]').attr('content'),
                                        _method: 'PATCH'
                                    },
                                    success: function(response) {
                                        Swal.fire({
                                            title: 'Sucesso!',
                                            text: 'Tarefa concluída com sucesso!',
                                            icon: 'success',
                                            confirmButtonText: 'Ok',
                                            background: getComputedStyle(document.documentElement).getPropertyValue('--surface-color'),
                                            color: getComputedStyle(document.documentElement).getPropertyValue('--text-primary'),
                                            customClass: {
                                                confirmButton: 'swal-confirm-button',
                                                title: 'swal-title',
                                                htmlContainer: 'swal-html-container'
                                            }
                                        }).then(() => {
                                            // Recarregar a página após conclusão
                                            window.location.reload();
                                        });
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Erro ao concluir a tarefa:', error);
                                        
                                        Swal.fire({
                                            title: 'Erro!',
                                            text: 'Não foi possível concluir a tarefa.',
                                            icon: 'error',
                                            confirmButtonText: 'Ok',
                                            background: getComputedStyle(document.documentElement).getPropertyValue('--surface-color'),
                                            color: getComputedStyle(document.documentElement).getPropertyValue('--text-primary')
                                        });
                                    }
                                });
                            } else {
                                Swal.fire({
                                    title: 'Não é possível concluir',
                                    text: 'Esta tarefa possui subtarefas pendentes!',
                                    icon: 'warning',
                                    confirmButtonText: 'Ok',
                                    background: getComputedStyle(document.documentElement).getPropertyValue('--surface-color'),
                                    color: getComputedStyle(document.documentElement).getPropertyValue('--text-primary'),
                                    customClass: {
                                        confirmButton: 'swal-confirm-button',
                                        title: 'swal-title',
                                        htmlContainer: 'swal-html-container'
                                    }
                                });
                            }
                        }
                    });
                });
                
                // Excluir tarefa com SweetAlert
                $(document).on('click', '.delete-task-btn, .delete-task-btn i', function(e) {
                    e.preventDefault();
                    e.stopPropagation(); // Impedir que o clique se propague para a linha da tabela
                    
                    const taskId = $(this).data('task-id');
                    const taskTitle = $(this).data('task-title');
                    const $form = $(this).closest('.delete-task-form');
                    
                    Swal.fire({
                        title: 'Tem certeza?',
                        html: `Você deseja excluir a tarefa <strong>"${taskTitle}"</strong>?<br><small class="text-danger">Esta ação não pode ser desfeita.</small>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sim, excluir',
                        cancelButtonText: 'Cancelar',
                        background: getComputedStyle(document.documentElement).getPropertyValue('--surface-color'),
                        color: getComputedStyle(document.documentElement).getPropertyValue('--text-primary'),
                        customClass: {
                            confirmButton: 'swal-confirm-button',
                            cancelButton: 'swal-cancel-button',
                            title: 'swal-title',
                            htmlContainer: 'swal-html-container'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $form.submit();
                        }
                    });
                });

            });
        }, 100);
    });
});
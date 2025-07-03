// Garantir que o CSRF token esteja disponível para todas as requisições AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function() {
    // Variáveis para controle de filtros
    let activeFilters = {
        search: '',
        category: 'all',
        sort: 'none'
    };

    // Initialize tooltips
    $('[title]').tooltip({
        placement: 'top',
        trigger: 'hover',
        container: 'body'
    });

    // ==== PESQUISA E FILTROS ====
    
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
    
    // Filtro por categoria
    $(document).on('click', '.dropdown-item[data-category]', function(e) {
        e.preventDefault();
        const category = $(this).data('category');
        
        activeFilters.category = category;
        
        $('#categoryFilterBtn').html(`<i class="fas fa-tag"></i> ${$(this).text()}`);
        applyFilters();
        updateActiveFiltersDisplay();
    });
    
    // Ordenação por data
    $(document).on('click', '.dropdown-item[data-sort]', function(e) {
        e.preventDefault();
        const sort = $(this).data('sort');
        activeFilters.sort = sort;
        
        $('#dateFilterBtn').html(`<i class="fas fa-sort"></i> ${$(this).text()}`);
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
            sort: 'none'
        };
        
        // Resetar inputs e botões
        $('#task-search').val('');
        $('#categoryFilterBtn').html('<i class="fas fa-tag"></i> Categorias');
        $('#dateFilterBtn').html('<i class="fas fa-sort"></i> Ordenar');
        
        // Mostrar todas as tarefas
        $('.task-row').show();
        applyFiltersSorting();
        updateActiveFiltersDisplay();
    });

    // Funções para controle dos filtros
    function updateActiveFiltersDisplay() {
        let filterTags = [];
        
        if (activeFilters.search) {
            filterTags.push(`<span class="filter-tag" data-filter="search">Pesquisa: "${activeFilters.search}" <i class="fas fa-times"></i></span>`);
        }
        
        if (activeFilters.category !== 'all') {
            let categoryName = '';
            if (activeFilters.category === 'none') {
                categoryName = 'Sem categoria';
            } else {
                categoryName = $(`.dropdown-item[data-category="${activeFilters.category}"]`).text();
            }
            filterTags.push(`<span class="filter-tag" data-filter="category">Categoria: ${categoryName} <i class="fas fa-times"></i></span>`);
        }
        
        if (activeFilters.sort !== 'none') {
            let sortName = $(`.dropdown-item[data-sort="${activeFilters.sort}"]`).text();
            filterTags.push(`<span class="filter-tag" data-filter="sort">Ordenação: ${sortName} <i class="fas fa-times"></i></span>`);
        }
        
        if (filterTags.length > 0) {
            $('#filter-tags').html(filterTags.join(' '));
            $('#active-filters').removeClass('d-none');
        } else {
            $('#active-filters').addClass('d-none');
        }
    }
    
    // Remover filtro individual
    $(document).on('click', '.filter-tag i', function() {
        const filterType = $(this).parent().data('filter');
        
        switch (filterType) {
            case 'category':
                activeFilters.category = 'all';
                $('#categoryFilterBtn').html('<i class="fas fa-tag"></i> Categorias');
                break;
            case 'sort':
                activeFilters.sort = 'none';
                $('#dateFilterBtn').html('<i class="fas fa-sort"></i> Ordenar');
                break;
            case 'search':
                activeFilters.search = '';
                $('#task-search').val('');
                break;
        }
        
        applyFilters();
        updateActiveFiltersDisplay();
    });

    // Aplicar todos os filtros
    function applyFilters() {
        $('.task-row').each(function() {
            const $row = $(this);
            let visible = true;
            
            // Filtro de pesquisa
            if (activeFilters.search) {
                const title = $row.find('.task-title').text().toLowerCase();
                if (!title.includes(activeFilters.search)) {
                    visible = false;
                }
            }
            
            // Filtro de categoria
            if (activeFilters.category !== 'all' && visible) {
                const categoryIds = $row.data('category-ids') ? $row.data('category-ids').toString().split(',') : [];
                
                if (activeFilters.category === 'none') {
                    if (categoryIds.length > 0) {
                        visible = false;
                    }
                } else {
                    if (!categoryIds.includes(activeFilters.category.toString())) {
                        visible = false;
                    }
                }
            }
            
            $row.toggle(visible);
        });
        
        applyFiltersSorting();
        updateNoTasksMessage();
    }
    
    // Aplicar ordenação
    function applyFiltersSorting() {
        if (activeFilters.sort === 'none') {
            resetOriginalOrder();
            return;
        }
        
        const $table = $('.table');
        const $rows = $('.task-row:visible').detach().get();
        
        $rows.sort(function(a, b) {
            if (activeFilters.sort === 'date-asc') {
                return compareDates($(a).data('completion-date'), $(b).data('completion-date'), true);
            } else if (activeFilters.sort === 'date-desc') {
                return compareDates($(a).data('completion-date'), $(b).data('completion-date'), false);
            }
            return 0;
        });
        
        $table.find('tbody').append($rows);
    }
    
    // Comparar datas para ordenação
    function compareDates(a, b, ascending) {
        const dateA = new Date(a);
        const dateB = new Date(b);
        return ascending ? dateA - dateB : dateB - dateA;
    }
    
    // Restaurar ordem original
    function resetOriginalOrder() {
        const $table = $('.table');
        const $rows = $('.task-row').detach().get();
        
        $rows.sort(function(a, b) {
            return $(a).data('original-index') - $(b).data('original-index');
        });
        
        $table.find('tbody').append($rows);
    }
    
    // Atualizar mensagem de "sem tarefas"
    function updateNoTasksMessage() {
        const visibleRows = $('.task-row:visible').length;
        
        if (visibleRows === 0) {
            if ($('.no-results-row').length === 0) {
                const colspan = $('.table thead th').length;
                const $noResultsRow = $(`
                    <tr class="no-results-row">
                        <td colspan="${colspan}" class="text-center">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle" style="margin-right: 0.5rem;"></i>
                                Nenhuma tarefa encontrada com os filtros aplicados.
                            </div>
                        </td>
                    </tr>
                `);
                $('.table tbody').append($noResultsRow);
            }
        } else {
            $('.no-results-row').remove();
        }
    }

    // Botão de limpar todas tarefas
    $('#clear-tasks-btn').on('click', function() {
        Swal.fire({
            title: 'Limpar tarefas concluídas',
            html: 'Todas as tarefas concluídas serão excluídas permanentemente.<br><small class="text-danger">Esta ação não pode ser desfeita!</small>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, excluir todas',
            cancelButtonText: 'Cancelar',
            background: getComputedStyle(document.documentElement).getPropertyValue('--surface-color'),
            color: getComputedStyle(document.documentElement).getPropertyValue('--text-primary'),
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = uncompleteRoutes.clearAll;
            }
        }).catch(error => {
            console.error('SweetAlert error:', error);
        });
    });
    
    // Botão de excluir tarefa individual
    $(document).on('click', '.delete-task-btn, .delete-task-btn i', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
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
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                $form.submit();
            }
        }).catch(error => {
            console.error('SweetAlert error:', error);
        });
    });
    
    // Adicionar SweetAlert para retornar tarefa ao estado pendente
    $(document).on('click', '.btn-warning.action-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const $form = $(this).closest('form');
        const taskTitle = $(this).closest('tr').find('.task-title').text();
        
        Swal.fire({
            title: 'Retornar tarefa?',
            html: `Deseja retornar a tarefa <strong>"${taskTitle}"</strong> para o estado pendente?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, retornar',
            cancelButtonText: 'Cancelar',
            background: getComputedStyle(document.documentElement).getPropertyValue('--surface-color'),
            color: getComputedStyle(document.documentElement).getPropertyValue('--text-primary'),
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                $form.submit();
            }
        }).catch(error => {
            console.error('SweetAlert error:', error);
        });
    });
});
// Garantir que o CSRF token esteja disponível para todas as requisições AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function() {
    // Abrir modal de edição quando o botão for clicado
    $('.edit-category-btn').on('click', function() {
        const categoryId = $(this).data('category-id');
        
        // Buscar dados da categoria via AJAX
        $.ajax({
            url: `/categories/${categoryId}/edit`,
            type: 'GET',
            success: function(data) {
                // Preencher o formulário com os dados recebidos
                $('#edit-category-id').val(data.id);
                $('#edit-name').val(data.name);
                $('#edit-description').val(data.description);
                $('#edit-color').val(data.color);
                
                // Abrir o modal
                $('#editCategoryModal').modal('show');
            },
            error: function() {
                Swal.fire({
                    title: 'Erro!',
                    text: 'Não foi possível carregar os dados da categoria.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    background: getComputedStyle(document.documentElement).getPropertyValue('--surface-color'),
                    color: getComputedStyle(document.documentElement).getPropertyValue('--text-primary'),
                    customClass: {
                        confirmButton: 'swal-confirm-button',
                        title: 'swal-title',
                        htmlContainer: 'swal-html-container'
                    }
                });
            }
        });
    });
    
    // Salvar alterações da categoria
    $('#save-edit-category-btn').on('click', function() {
        const categoryId = $('#edit-category-id').val();
        const formData = {
            name: $('#edit-name').val(),
            description: $('#edit-description').val(),
            color: $('#edit-color').val(),
            _token: $('meta[name="csrf-token"]').attr('content'),
            _method: 'PUT'
        };
        
        $.ajax({
            url: `/categories/${categoryId}`,
            type: 'POST',
            data: formData,
            success: function(response) {
                $('#editCategoryModal').modal('hide');
                
                Swal.fire({
                    title: 'Sucesso!',
                    text: 'Categoria atualizada com sucesso!',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    background: getComputedStyle(document.documentElement).getPropertyValue('--surface-color'),
                    color: getComputedStyle(document.documentElement).getPropertyValue('--text-primary'),
                    customClass: {
                        confirmButton: 'swal-confirm-button',
                        title: 'swal-title',
                        htmlContainer: 'swal-html-container'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.reload();
                    }
                });
            },
            error: function(xhr) {
                let errorMessage = 'Ocorreu um erro ao atualizar a categoria.';
                
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                }
                
                Swal.fire({
                    title: 'Erro!',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'OK',
                    background: getComputedStyle(document.documentElement).getPropertyValue('--surface-color'),
                    color: getComputedStyle(document.documentElement).getPropertyValue('--text-primary'),
                    customClass: {
                        confirmButton: 'swal-confirm-button',
                        title: 'swal-title',
                        htmlContainer: 'swal-html-container'
                    }
                });
            }
        });
    });
    
    // Confirmação para excluir categoria
    $('.delete-category-btn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation(); // Impedir que o clique se propague
        
        const categoryId = $(this).data('category-id');
        const categoryName = $(this).data('category-name');
        const deleteForm = $(this).closest('form');
        
        Swal.fire({
            title: 'Tem certeza?',
            html: `Você deseja excluir a categoria <strong>"${categoryName}"</strong>?<br><small class="text-danger">Esta ação não pode ser desfeita.</small>`,
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
                deleteForm.submit();
            }
        });
    });
    
    // Abrir modal de criação quando o botão for clicado
    $('.add-category-button').on('click', function(e) {
        e.preventDefault();
        $('#createCategoryModal').modal('show');
    });
    
    // Criar nova categoria
    $('#create-category-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            name: $('#create-name').val(),
            description: $('#create-description').val(),
            color: $('#create-color').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        $.ajax({
            url: '/categories',
            type: 'POST',
            data: formData,
            success: function(response) {
                $('#createCategoryModal').modal('hide');
                
                Swal.fire({
                    title: 'Sucesso!',
                    text: 'Categoria criada com sucesso!',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    background: getComputedStyle(document.documentElement).getPropertyValue('--surface-color'),
                    color: getComputedStyle(document.documentElement).getPropertyValue('--text-primary'),
                    customClass: {
                        confirmButton: 'swal-confirm-button',
                        title: 'swal-title',
                        htmlContainer: 'swal-html-container'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.reload();
                    }
                });
            },
            error: function(xhr) {
                let errorMessage = 'Ocorreu um erro ao criar a categoria.';
                
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                }
                
                Swal.fire({
                    title: 'Erro!',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'OK',
                    background: getComputedStyle(document.documentElement).getPropertyValue('--surface-color'),
                    color: getComputedStyle(document.documentElement).getPropertyValue('--text-primary'),
                    customClass: {
                        confirmButton: 'swal-confirm-button',
                        title: 'swal-title',
                        htmlContainer: 'swal-html-container'
                    }
                });
            }
        });
    });
});
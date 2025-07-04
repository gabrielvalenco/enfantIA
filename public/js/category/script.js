// Garantir que o CSRF token esteja disponível para todas as requisições AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// Helper function to convert RGB to HEX
function rgbToHex(rgb) {
    // If it's already in hex format, return as is
    if (rgb.startsWith('#')) return rgb;
    
    // Extract the r, g, b values from rgb() string
    const result = /^rgba?\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/.exec(rgb);
    if (!result) return '#000000';
    
    // Convert each component to hex and pad with leading zero if needed
    const r = parseInt(result[1]).toString(16).padStart(2, '0');
    const g = parseInt(result[2]).toString(16).padStart(2, '0');
    const b = parseInt(result[3]).toString(16).padStart(2, '0');
    
    return `#${r}${g}${b}`.toUpperCase();
}

// Modal functionality
function openModal(modalId) {
    document.getElementById(modalId).style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside content
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        // Fechar apenas o modal específico que foi clicado, não todos os modais
        event.target.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
};

$(document).ready(function() {
    // Handle edit buttons
    $('.edit-badge').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation(); // Prevent row selection when clicking edit
        
        const categoryId = $(this).data('category-id');
        const row = $(this).closest('tr');
        
        // Get category data from the row
        const name = row.find('td:first-child').text().trim();
        // Use data attribute for full description instead of truncated text
        const description = $(this).data('category-description') || row.find('td:nth-child(2)').text().trim();
        const color = row.find('.color-preview').css('background-color');
        
        // Fill the edit form
        $('#edit-category-id').val(categoryId);
        $('#edit-name').val(name);
        $('#edit-description').val(description);
        $('#edit-color').val(rgbToHex(color));
        
        // Update color preview
        $('#edit-color-preview').css('background-color', rgbToHex(color));
        
        openModal('editCategoryModal');
    });
    // Confirmação para excluir categoria
    $('.delete-badge').on('click', function(e) {
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
    $('.add-button').on('click', function(e) {
        e.preventDefault();
        openModal('createCategoryModal');
    });
    
    // Criar nova categoria
    $('#save-create-category-btn').on('click', function(e) {
        e.preventDefault();
        
        // Mostrar indicador de carregamento
        const $button = $(this);
        const originalButtonText = $button.html();
        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Criando...');
        
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
                closeModal('createCategoryModal');
                
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
            },
            complete: function() {
                // Restaurar o estado original do botão
                $button.prop('disabled', false).html(originalButtonText);
                
                // Garantir que o modal de criação seja fechado em caso de erro
                if ($('#createCategoryModal').is(':visible')) {
                    closeModal('createCategoryModal');
                }
            }
        });
    });
    
    // Editar categoria
    $('#save-edit-category-btn').on('click', function(e) {
        e.preventDefault();
        const categoryId = $('#edit-category-id').val();
        const formData = {
            name: $('#edit-name').val(),
            description: $('#edit-description').val(),
            color: $('#edit-color').val(),
            _token: $('meta[name="csrf-token"]').attr('content'),
            _method: 'PUT'
        };
        
        // Mostrar indicador de carregamento
        const $button = $(this);
        const originalButtonText = $button.html();
        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Salvando...');
        
        $.ajax({
            url: `/categories/${categoryId}`,
            type: 'POST',
            data: formData,
            success: function(response) {
                // Ensure we close the modal properly
                closeModal('editCategoryModal');
                
                // Garantir que o modal de criação não seja exibido
                if (document.getElementById('createCategoryModal').style.display === 'flex') {
                    closeModal('createCategoryModal');
                }
                
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
                    // Reload the page after success message
                    window.location.reload();
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
            },
            complete: function() {
                // Restaurar o estado original do botão
                $button.prop('disabled', false).html(originalButtonText);
                
                // Make sure modal is closed in case of error
                if ($('#editCategoryModal').is(':visible')) {
                    closeModal('editCategoryModal');
                }
                
                // Garantir que o modal de criação não seja exibido
                if ($('#createCategoryModal').is(':visible')) {
                    closeModal('createCategoryModal');
                }
            }
        });
    });
});
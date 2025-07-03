document.addEventListener('DOMContentLoaded', function() {
    // Configurar os botões de excluir grupo
    const deleteButtons = document.querySelectorAll('.delete-group-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('form');
            
            Swal.fire({
                title: 'Tem certeza?',
                text: 'Esta ação excluirá permanentemente o grupo e todas as suas tarefas!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: 'var(--link-color)',
                cancelButtonColor: 'var(--text-secondary)',
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
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
    
    // Configurar os botões de sair do grupo
    const leaveButtons = document.querySelectorAll('.leave-group-btn');
    leaveButtons.forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('form');
            const groupId = this.getAttribute('data-group-id');
            
            Swal.fire({
                title: 'Confirmar saída',
                text: 'Tem certeza que deseja sair deste grupo?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: 'var(--link-color)',
                cancelButtonColor: 'var(--text-secondary)',
                confirmButtonText: 'Sim, sair',
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
                    // Se estamos na página de listagem, o botão não tem um formulário associado
                    // Então criamos e enviamos um formulário via JavaScript
                    if (groupId) {
                        // Criar e enviar formulário via JavaScript
                        const formDynamic = document.createElement('form');
                        formDynamic.method = 'POST';
                        formDynamic.action = '/groups/' + groupId + '/leave';
                        formDynamic.style.display = 'none';
                        
                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        formDynamic.appendChild(csrfToken);
                        
                        const methodField = document.createElement('input');
                        methodField.type = 'hidden';
                        methodField.name = '_method';
                        methodField.value = 'DELETE';
                        formDynamic.appendChild(methodField);
                        
                        document.body.appendChild(formDynamic);
                        formDynamic.submit();
                    } else if (form) {
                        // Se estamos em outra página com formulário já configurado
                        form.submit();
                    }
                }
            });
        });
    });
});
document.addEventListener('DOMContentLoaded', function() {
    // Adicionar cursor pointer nas tarefas
    document.querySelectorAll('.task-item').forEach(item => {
        item.style.cursor = 'pointer';
    });
    
    // Botão para excluir grupo
    const deleteGroupBtn = document.getElementById('delete-group-btn');
    if (deleteGroupBtn) {
        deleteGroupBtn.addEventListener('click', function() {
            showConfirmDialog(
                'Excluir Grupo',
                'Tem certeza que deseja excluir este grupo? Esta ação excluirá permanentemente o grupo e todas as suas tarefas!',
                'warning',
                function() {
                    // Criar e enviar formulário via JavaScript
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/groups/' + groupId + '/delete';
                    form.style.display = 'none';
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = csrfTokenValue;
                    form.appendChild(csrfToken);
                    
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';
                    form.appendChild(methodField);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            );
        });
    }
    
    // Botão para sair do grupo
    const leaveGroupBtn = document.getElementById('leave-group-btn');
    if (leaveGroupBtn) {
        leaveGroupBtn.addEventListener('click', function() {
            showConfirmDialog(
                'Sair do Grupo',
                'Tem certeza que deseja sair deste grupo? Você não terá mais acesso às tarefas e informações do grupo.',
                'question',
                function() {
                    // Criar e enviar formulário via JavaScript
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/groups/' + groupId + '/leave';
                    form.style.display = 'none';
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = csrfTokenValue;
                    form.appendChild(csrfToken);
                    
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';
                    form.appendChild(methodField);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            );
        });
    }
    
    // Lidar com cliques em tarefas para mostrar detalhes
    document.querySelectorAll('.task-item').forEach(taskItem => {
        taskItem.addEventListener('click', function(e) {
            // Ignorar cliques nos botões de ação
            if (e.target.closest('.task-actions')) {
                return;
            }
            
            const taskId = this.dataset.taskId;
            const taskTitle = this.querySelector('.task-title').textContent.trim();
            const taskCreator = this.dataset.taskCreator;
            const taskDate = this.dataset.taskDate;
            const taskDescription = this.dataset.taskDescription || 'Sem descrição';
            const taskAssignee = this.dataset.taskAssignee || null;
            const isCompleted = this.dataset.taskCompleted === '1';
            
            // Fill the modal with task details
            document.getElementById('modal-task-title').textContent = taskTitle;
            document.getElementById('modal-task-creator').textContent = taskCreator;
            document.getElementById('modal-task-date').textContent = taskDate;
            document.getElementById('modal-task-description').textContent = taskDescription;
            
            // Update status badge
            const statusElement = document.getElementById('modal-task-status');
            if (isCompleted) {
                statusElement.textContent = 'Concluída';
                statusElement.className = 'status-complete';
            } else {
                statusElement.textContent = 'Pendente';
                statusElement.className = 'status-pending';
            }
            
            // Show/hide assignee row
            const assigneeRow = document.getElementById('modal-assignee-row');
            if (taskAssignee) {
                assigneeRow.style.display = '';
                document.getElementById('modal-task-assignee').textContent = taskAssignee;
            } else {
                assigneeRow.style.display = 'none';
            }
            
            // Add action buttons
            document.getElementById('modal-complete-action').innerHTML = '';
            document.getElementById('modal-edit-action').innerHTML = '';
            document.getElementById('modal-delete-action').innerHTML = '';
            
            // Complete button (only for non-completed tasks)
            if (!isCompleted) {
                const completeForm = document.createElement('form');
                completeForm.method = 'POST';
                completeForm.action = `/tasks/${taskId}/complete`;
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PATCH';
                
                const submitButton = document.createElement('button');
                submitButton.type = 'submit';
                submitButton.className = 'btn btn-complete';
                submitButton.innerHTML = '<i class="fas fa-check me-2"></i> Marcar como concluída';
                
                completeForm.appendChild(csrfInput);
                completeForm.appendChild(methodInput);
                completeForm.appendChild(submitButton);
                
                document.getElementById('modal-complete-action').appendChild(completeForm);
            }
            
            // Edit button
            const editButton = document.createElement('a');
            editButton.href = `/tasks/${taskId}/edit`;
            editButton.className = 'btn btn-edit';
            editButton.innerHTML = '<i class="fas fa-edit me-2"></i> Editar';
            document.getElementById('modal-edit-action').appendChild(editButton);
            
            // Delete button
            const deleteForm = document.createElement('form');
            deleteForm.method = 'POST';
            deleteForm.action = `/tasks/${taskId}`;
            deleteForm.onsubmit = function(e) {
                e.preventDefault();
                showDeleteConfirmation(() => {
                    this.submit();
                });
            };
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            
            const deleteButton = document.createElement('button');
            deleteButton.type = 'submit';
            deleteButton.className = 'btn btn-delete-task';
            deleteButton.innerHTML = '<i class="fas fa-trash me-2"></i> Excluir';
            
            deleteForm.appendChild(csrfInput);
            deleteForm.appendChild(methodInput);
            deleteForm.appendChild(deleteButton);
            
            document.getElementById('modal-delete-action').appendChild(deleteForm);
            
            // Show the modal
            const modalElement = document.getElementById('taskDetailsModal');
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
            
            // Add animation class
            modalElement.addEventListener('shown.bs.modal', function() {
                document.querySelector('.task-details-container').classList.add('fade-in');
            });
        });
    });
    
    // Verificação de email para adicionar membro
    const checkEmailBtn = document.getElementById('checkEmailBtn');
    const emailInput = document.getElementById('email');
    const emailFeedback = document.getElementById('emailFeedback');
    const addMemberBtn = document.getElementById('addMemberBtn');
    
    if (checkEmailBtn && emailInput && emailFeedback) {
        checkEmailBtn.addEventListener('click', function() {
            const email = emailInput.value.trim();
            
            if (!email) {
                showFeedback(emailFeedback, 'Por favor, digite um email.', 'error');
                return;
            }
            
            // Verificar formato do email
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                showFeedback(emailFeedback, 'Por favor, digite um email válido.', 'error');
                return;
            }
            
            // Verificar se o email é do usuário atual
            if (email === userEmail) {
                showFeedback(emailFeedback, 'Você não pode adicionar a si mesmo.', 'error');
                return;
            }
            
            // Verificar se o usuário existe
            fetch(`/api/check-user?email=${encodeURIComponent(email)}`)
                .then(response => response.json())
                .then(data => {
                    if (!data.exists) {
                        showFeedback(emailFeedback, 'Este usuário não existe no sistema.', 'error');
                        addMemberBtn.disabled = true;
                    } else if (data.inGroup) {
                        showFeedback(emailFeedback, 'Este usuário já é membro do grupo.', 'error');
                        addMemberBtn.disabled = true;
                    } else {
                        showFeedback(emailFeedback, 'Usuário encontrado! Você pode adicioná-lo ao grupo.', 'success');
                        addMemberBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Erro ao verificar usuário:', error);
                    showFeedback(emailFeedback, 'Erro ao verificar usuário. Tente novamente.', 'error');
                });
        });
    }
    
    // Função para mostrar feedback
    function showFeedback(element, message, type) {
        element.innerHTML = '';
        const feedbackDiv = document.createElement('div');
        feedbackDiv.className = `feedback ${type}`;
        feedbackDiv.textContent = message;
        element.appendChild(feedbackDiv);
    }
    
    // Função para mostrar diálogo de confirmação
    function showConfirmDialog(title, message, icon, confirmCallback) {
        const dialogOverlay = document.createElement('div');
        dialogOverlay.className = 'dialog-overlay';
        
        const dialogBox = document.createElement('div');
        dialogBox.className = 'dialog-box';
        
        const dialogHeader = document.createElement('div');
        dialogHeader.className = 'dialog-header';
        
        const dialogTitle = document.createElement('h3');
        dialogTitle.textContent = title;
        dialogHeader.appendChild(dialogTitle);
        
        const dialogBody = document.createElement('div');
        dialogBody.className = 'dialog-body';
        
        const iconElement = document.createElement('i');
        iconElement.className = `fas fa-${icon === 'warning' ? 'exclamation-triangle' : 'question-circle'} dialog-icon ${icon}`;
        
        const messageElement = document.createElement('p');
        messageElement.textContent = message;
        
        dialogBody.appendChild(iconElement);
        dialogBody.appendChild(messageElement);
        
        const dialogFooter = document.createElement('div');
        dialogFooter.className = 'dialog-footer';
        
        const cancelButton = document.createElement('button');
        cancelButton.className = 'btn btn-cancel';
        cancelButton.textContent = 'Cancelar';
        cancelButton.addEventListener('click', function() {
            document.body.removeChild(dialogOverlay);
        });
        
        const confirmButton = document.createElement('button');
        confirmButton.className = 'btn btn-confirm';
        confirmButton.textContent = icon === 'warning' ? 'Excluir' : 'Confirmar';
        confirmButton.addEventListener('click', function() {
            document.body.removeChild(dialogOverlay);
            confirmCallback();
        });
        
        dialogFooter.appendChild(cancelButton);
        dialogFooter.appendChild(confirmButton);
        
        dialogBox.appendChild(dialogHeader);
        dialogBox.appendChild(dialogBody);
        dialogBox.appendChild(dialogFooter);
        
        dialogOverlay.appendChild(dialogBox);
        document.body.appendChild(dialogOverlay);
        
        // Animação de entrada
        setTimeout(() => {
            dialogBox.style.transform = 'translateY(0)';
            dialogBox.style.opacity = '1';
        }, 10);
    }
    
    // Função para mostrar confirmação de exclusão
    function showDeleteConfirmation(confirmCallback) {
        showConfirmDialog(
            'Excluir Tarefa',
            'Tem certeza que deseja excluir esta tarefa? Esta ação não pode ser desfeita.',
            'warning',
            confirmCallback
        );
    }
    
    // Função para mostrar notificações toast
    function showToast(message, type = 'info') {
        const toastContainer = document.querySelector('.toast-container');
        
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        
        const toastContent = document.createElement('div');
        toastContent.className = 'toast-content';
        
        const icon = document.createElement('i');
        icon.className = `fas ${getIconForType(type)} toast-icon`;
        
        const messageElement = document.createElement('span');
        messageElement.className = 'toast-message';
        messageElement.textContent = message;
        
        const closeButton = document.createElement('button');
        closeButton.className = 'toast-close';
        closeButton.innerHTML = '&times;';
        closeButton.addEventListener('click', function() {
            removeToast(toast);
        });
        
        toastContent.appendChild(icon);
        toastContent.appendChild(messageElement);
        toast.appendChild(toastContent);
        toast.appendChild(closeButton);
        
        toastContainer.appendChild(toast);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            removeToast(toast);
        }, 5000);
    }
    
    function removeToast(toast) {
        toast.classList.add('toast-hide');
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }
    
    function getIconForType(type) {
        switch (type) {
            case 'success': return 'fa-check-circle';
            case 'error': return 'fa-exclamation-circle';
            case 'warning': return 'fa-exclamation-triangle';
            default: return 'fa-info-circle';
        }
    }
});
document.addEventListener('DOMContentLoaded', function() {
    // Elementos do modal
    const modal = document.getElementById('note-modal');
    const openModalBtn = document.getElementById('open-note-modal');
    const closeModalBtn = document.querySelector('.close-modal');
    const cancelBtn = document.getElementById('cancel-note');
    const noteForm = document.getElementById('note-form');
    const modalTitle = document.getElementById('modal-title');
    
    // Campos do formulário
    const noteId = document.getElementById('note-id');
    const noteTitle = document.getElementById('note-title');
    const noteContent = document.getElementById('note-content');
    const taskId = document.getElementById('task-id');
    const categoryId = document.getElementById('category-id');
    
    // Contadores de caracteres
    const titleCounter = document.getElementById('title-counter');
    const contentCounter = document.getElementById('content-counter');
    
    // Abrir modal para nova nota
    openModalBtn.addEventListener('click', function() {
        resetForm();
        modalTitle.textContent = 'Nova Nota';
        modal.style.display = 'block';
    });
    
    // Fechar modal
    function closeModal() {
        modal.style.display = 'none';
    }
    
    closeModalBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);
    
    // Fechar modal ao clicar fora dele
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            closeModal();
        }
    });
    
    // Resetar formulário
    function resetForm() {
        noteForm.reset();
        noteId.value = '';
        noteForm.action = '/notes';
        
        // Remover campo _method se existir
        const methodField = noteForm.querySelector('input[name="_method"]');
        if (methodField) {
            methodField.remove();
        }
        
        updateCharCounters();
    }
    
    // Atualizar contadores de caracteres
    function updateCharCounters() {
        if (titleCounter) titleCounter.textContent = noteTitle.value.length;
        if (contentCounter) contentCounter.textContent = noteContent.value.length;
    }
    
    // Event listeners para os contadores de caracteres
    if (noteTitle) {
        noteTitle.addEventListener('input', function() {
            titleCounter.textContent = this.value.length;
        });
    }
    
    if (noteContent) {
        noteContent.addEventListener('input', function() {
            contentCounter.textContent = this.value.length;
        });
    }
    
    // Submeter formulário de nota com AJAX
    if (noteForm) {
        noteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const isUpdate = noteId.value !== '';
            const url = isUpdate ? `/notes/${noteId.value}` : '/notes';
            const method = isUpdate ? 'PUT' : 'POST';
            
            // Adicionar método se for atualização
            if (isUpdate && !formData.has('_method')) {
                formData.append('_method', 'PUT');
            }
            
            // Obter token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Fazer requisição AJAX
            fetch(url, {
                method: 'POST', // Sempre POST para formulários, mas com _method para PUT
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Fechar modal
                    closeModal();
                    
                    // Mostrar mensagem de sucesso
                    Swal.fire({
                        title: isUpdate ? 'Nota atualizada!' : 'Nota criada!',
                        text: isUpdate ? 'Sua nota foi atualizada com sucesso.' : 'Sua nota foi criada com sucesso.',
                        icon: 'success',
                        confirmButtonColor: 'var(--link-color)',
                        confirmButtonText: 'Ok'
                    }).then(() => {
                        // Recarregar a página para mostrar a nota nova/atualizada
                        window.location.reload();
                    });
                } else {
                    // Mostrar mensagem de erro
                    Swal.fire({
                        title: 'Erro!',
                        text: data.message || 'Ocorreu um erro ao processar sua solicitação.',
                        icon: 'error',
                        confirmButtonColor: 'var(--link-color)',
                        confirmButtonText: 'Ok'
                    });
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                Swal.fire({
                    title: 'Erro!',
                    text: 'Ocorreu um erro ao processar sua solicitação.',
                    icon: 'error',
                    confirmButtonColor: 'var(--link-color)',
                    confirmButtonText: 'Ok'
                });
            });
        });
    }
    
    // Editar nota
    document.addEventListener('click', function(e) {
        if (e.target.closest('.note-edit')) {
            const button = e.target.closest('.note-edit');
            const noteId = button.getAttribute('data-id');
            editNote(noteId);
        }
    });
    
    function editNote(id) {
        // Obter token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`/notes/${id}/edit`, {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.note) {
                const note = data.note;
                
                // Atualizar formulário
                noteId.value = note.id;
                noteTitle.value = note.title;
                noteContent.value = note.content;
                
                if (taskId) {
                    taskId.value = note.task_id || '';
                }
                
                if (categoryId) {
                    categoryId.value = note.category_id || '';
                }
                
                // Atualizar contadores
                updateCharCounters();
                
                // Atualizar URL do formulário
                noteForm.action = `/notes/${note.id}`;
                
                // Adicionar método PUT
                let methodField = noteForm.querySelector('input[name="_method"]');
                if (!methodField) {
                    methodField = document.createElement('input');
                    methodField.setAttribute('type', 'hidden');
                    methodField.setAttribute('name', '_method');
                    noteForm.appendChild(methodField);
                }
                methodField.value = 'PUT';
                
                // Abrir modal
                modalTitle.textContent = 'Editar Nota';
                modal.style.display = 'block';
            } else {
                // Mostrar mensagem de erro
                Swal.fire({
                    title: 'Erro!',
                    text: 'Não foi possível carregar os dados da nota.',
                    icon: 'error',
                    confirmButtonColor: 'var(--link-color)',
                    confirmButtonText: 'Ok'
                });
            }
        })
        .catch(error => {
            console.error('Erro ao buscar nota:', error);
            Swal.fire({
                title: 'Erro!',
                text: 'Ocorreu um erro ao tentar editar a nota.',
                icon: 'error',
                confirmButtonColor: 'var(--link-color)',
                confirmButtonText: 'Ok'
            });
        });
    }
    
    // Excluir nota
    document.addEventListener('click', function(e) {
        if (e.target.closest('.note-delete')) {
            const button = e.target.closest('.note-delete');
            const noteId = button.getAttribute('data-id');
            
            Swal.fire({
                title: 'Tem certeza?',
                text: 'Esta ação não pode ser desfeita!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: 'var(--delete-color)',
                cancelButtonColor: 'var(--border-color)',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteNote(noteId);
                }
            });
        }
    });
    
    function deleteNote(id) {
        // Obter token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`/notes/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mostrar mensagem de sucesso
                Swal.fire({
                    title: 'Excluída!',
                    text: 'Sua nota foi excluída com sucesso.',
                    icon: 'success',
                    confirmButtonColor: 'var(--link-color)',
                    confirmButtonText: 'Ok'
                });
                
                // Remover o card da nota do DOM
                const noteCard = document.querySelector(`.note-card[data-id="${id}"]`);
                if (noteCard) {
                    noteCard.remove();
                    
                    // Verificar se não existem mais notas
                    const notesContainer = document.querySelector('.notes-container');
                    if (notesContainer && notesContainer.children.length === 0) {
                        const noNotesDiv = document.createElement('div');
                        noNotesDiv.className = 'no-notes';
                        noNotesDiv.innerHTML = `
                            <i class="far fa-sticky-note"></i>
                            <p>Você ainda não tem notas. Crie uma nova nota para começar!</p>
                        `;
                        notesContainer.appendChild(noNotesDiv);
                    }
                }
            } else {
                // Mostrar mensagem de erro
                Swal.fire({
                    title: 'Erro!',
                    text: data.message || 'Ocorreu um erro ao excluir a nota.',
                    icon: 'error',
                    confirmButtonColor: 'var(--link-color)',
                    confirmButtonText: 'Ok'
                });
            }
        })
        .catch(error => {
            console.error('Erro ao excluir nota:', error);
            Swal.fire({
                title: 'Erro!',
                text: 'Ocorreu um erro ao excluir a nota.',
                icon: 'error',
                confirmButtonColor: 'var(--link-color)',
                confirmButtonText: 'Ok'
            });
        });
    }
    
    // Carregar categorias e tarefas via AJAX se estiverem vazios
    function loadTasksAndCategories() {
        if (taskId && taskId.options.length <= 1) {
            fetch('/tasks/list', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.tasks && data.tasks.length > 0) {
                    // Limpar opções existentes, exceto a primeira
                    while (taskId.options.length > 1) {
                        taskId.remove(1);
                    }
                    
                    // Adicionar novas opções
                    data.tasks.forEach(task => {
                        const option = document.createElement('option');
                        option.value = task.id;
                        option.textContent = task.title;
                        taskId.appendChild(option);
                    });
                }
            })
            .catch(error => console.error('Erro ao carregar tarefas:', error));
        }
        
        if (categoryId && categoryId.options.length <= 1) {
            fetch('/categories/list', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.categories && data.categories.length > 0) {
                    // Limpar opções existentes, exceto a primeira
                    while (categoryId.options.length > 1) {
                        categoryId.remove(1);
                    }
                    
                    // Adicionar novas opções
                    data.categories.forEach(category => {
                        const option = document.createElement('option');
                        option.value = category.id;
                        option.textContent = category.name;
                        option.style.color = category.color;
                        categoryId.appendChild(option);
                    });
                }
            })
            .catch(error => console.error('Erro ao carregar categorias:', error));
        }
    }
    
    // Carregar categorias e tarefas quando o modal é aberto
    openModalBtn.addEventListener('click', loadTasksAndCategories);
});

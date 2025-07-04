document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Sortable.js para drag and drop das notas
    initSortable();
    
    // Adicionar evento de clique nas notas para visualização
    initNoteViewEvents();
    
    // Elementos do modal
    const modal = document.getElementById('note-modal');
    const openModalBtn = document.getElementById('open-note-modal');
    const closeModalBtn = document.querySelector('.close-modal');
    const cancelBtn = document.getElementById('cancel-note');
    const saveBtn = document.getElementById('save-note');
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
    
    // Evento para o botão salvar que está fora do formulário
    if (saveBtn) {
        saveBtn.addEventListener('click', function() {
            // Dispara o evento de submit manualmente no formulário
            const submitEvent = new Event('submit', {
                bubbles: true,
                cancelable: true
            });
            noteForm.dispatchEvent(submitEvent);
        });
    }
    
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
    
    /**
     * Inicializa o Sortable.js para permitir arrastar e soltar as notas
     */
    function initSortable() {
        const notesContainer = document.querySelector('.notes-container');
        
        // Verificar se o container de notas existe e se há notas
        if (notesContainer && notesContainer.children.length > 0 && !notesContainer.querySelector('.no-notes')) {
            // Adicionar data-id a cada nota para rastreamento
            Array.from(notesContainer.querySelectorAll('.note-card')).forEach((noteCard, index) => {
                // Se o card já tem um ID de nota, usá-lo; caso contrário, usar o índice
                const noteId = noteCard.getAttribute('data-id') || index.toString();
                noteCard.setAttribute('data-id', noteId);
                noteCard.setAttribute('data-position', index);
            });
            
            // Remover classes antigas do cabeçalho caso existam
            document.querySelectorAll('.sortable-handle').forEach(el => {
                el.classList.remove('sortable-handle');
                if (el.classList.contains('note-header')) {
                    el.style.cursor = 'default';
                }
            });
            
            // Inicializar Sortable
            const sortable = new Sortable(notesContainer, {
                animation: 150,
                ghostClass: 'note-card-ghost',  // Classe aplicada ao clone durante arrasto
                chosenClass: 'note-card-chosen', // Classe aplicada ao elemento sendo arrastado
                dragClass: 'note-card-drag',     // Classe aplicada durante o arrasto
                handle: '.drag-handle',          // Usar o ícone de arrasto
                onEnd: function(evt) {
                    const noteId = evt.item.getAttribute('data-id');
                    const newPosition = evt.newIndex;
                    const oldPosition = evt.oldIndex;
                    
                    if (newPosition !== oldPosition) {
                        updateNotePositions(noteId, newPosition, oldPosition);
                    }
                }
            });
            
            // Adicionar estilos dinâmicos para drag and drop
            addDragStyles();
        }
    }
    
    /**
     * Atualiza as posições das notas após drag and drop
     */
    function updateNotePositions(movedNoteId, newPosition, oldPosition) {
        // Atualizar data-position em todas as notas
        const notes = Array.from(document.querySelectorAll('.note-card'));
        notes.forEach((note, index) => {
            note.setAttribute('data-position', index);
        });
        
        // Aqui você pode implementar uma chamada AJAX para salvar a ordem no servidor
        // Por exemplo:
        /*
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch('/notes/reorder', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                noteId: movedNoteId,
                newPosition: newPosition
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Feedback de sucesso
            }
        })
        .catch(error => console.error('Erro ao atualizar posições:', error));
        */
        
        // Feedback visual de sucesso
        Toast.fire({
            icon: 'success',
            title: 'Notas reorganizadas'
        });
    }
    
    /**
     * Adiciona estilos CSS necessários para o drag and drop
     */
    function addDragStyles() {
        // Verificar se os estilos já foram adicionados
        if (document.getElementById('drag-drop-styles')) return;
        
        // Criar elemento de estilo
        const style = document.createElement('style');
        style.id = 'drag-drop-styles';
        style.textContent = `
            .note-card-ghost {
                opacity: 0.5;
                background: var(--background-tertiary, #f0f0f0) !important;
            }
            
            .note-card-chosen {
                box-shadow: 0 0 10px 3px var(--link-color, rgb(32, 172, 130)) !important;
            }
            
            .note-card-drag {
                transform: scale(1.05);
                opacity: 0.8;
            }
            
            .sortable-handle {
                cursor: grab;
            }
            
            .sortable-handle:active {
                cursor: grabbing;
            }
            
            @media (max-width: 992px) {
                .notes-container {
                    grid-template-columns: repeat(2, 1fr) !important;
                }
            }
            
            @media (max-width: 576px) {
                .notes-container {
                    grid-template-columns: 1fr !important;
                }
            }
            
            .note-card .note-content, .note-card .note-header h3 {
                cursor: pointer;
            }
            
            .view-note-content {
                padding: 10px;
            }
            
            #view-note-content {
                white-space: pre-wrap;
                line-height: 1.6;
                min-height: 100px;
                margin-bottom: 20px;
            }
            
            .note-details {
                padding-top: 15px;
                margin-top: 15px;
            }
            
            .view-note-actions {
                display: flex;
                justify-content: flex-end;
                gap: 10px;
                margin-top: 20px;
            }
            
            .edit-button {
                background-color: var(--link-color);
                color: white;
                border: none;
                border-radius: 4px;
                padding: 8px 16px;
                cursor: pointer;
                transition: background-color 0.2s;
            }
            
            .edit-button:hover {
                background-color: var(--link-hover-color, #178f6a);
            }
            
            .mb-2 {
                margin-bottom: 8px;
            }
            
            .mb-4 {
                margin-bottom: 16px;
            }
            
            .mt-4 {
                margin-top: 16px;
            }
        `;
        
        document.head.appendChild(style);
    }
    
    /**
     * Inicializa eventos de visualização de notas ao clicar
     */
    function initNoteViewEvents() {
        // Modal de visualização e seus elementos
        const viewModal = document.getElementById('view-note-modal');
        const closeViewModalBtn = document.getElementById('close-view-modal');
        const closeViewBtn = document.getElementById('close-view-btn');
        const editBtn = document.getElementById('view-edit-btn');
        
        if (!viewModal) return; // Sair se o modal não existir
        
        // Evento para fechar modal de visualização
        function closeViewModal() {
            viewModal.style.display = 'none';
        }
        
        if (closeViewModalBtn) closeViewModalBtn.addEventListener('click', closeViewModal);
        if (closeViewBtn) closeViewBtn.addEventListener('click', closeViewModal);
        
        // Fechar ao clicar fora do modal
        window.addEventListener('click', function(event) {
            if (event.target === viewModal) {
                closeViewModal();
            }
        });
        
        // Evento de clique nas notas (exceto nos botões de ações)
        document.addEventListener('click', function(e) {
            // Verificar se o clique foi no conteúdo da nota ou no título
            const noteContent = e.target.closest('.note-content');
            const noteTitle = e.target.closest('.note-header h3');
            
            // Se não for conteúdo ou título, ou se for em botões de ações, sair
            if ((!noteContent && !noteTitle) || e.target.closest('.note-actions')) {
                return;
            }
            
            // Encontrar o card da nota para obter o ID
            const noteCard = e.target.closest('.note-card');
            if (noteCard) {
                const noteId = noteCard.getAttribute('data-id');
                if (noteId) {
                    viewNote(noteId);
                }
            }
        });
        
        // Botão Editar dentro do modal de visualização
        if (editBtn) {
            editBtn.addEventListener('click', function() {
                const noteId = this.getAttribute('data-id');
                if (noteId) {
                    closeViewModal();
                    editNote(noteId); // Usar a função existente de edição
                }
            });
        }
    }
    
    /**
     * Carrega e exibe os detalhes de uma nota no modal de visualização
     */
    function viewNote(id) {
        // Elementos do modal de visualização
        const viewModal = document.getElementById('view-note-modal');
        const viewNoteTitle = document.getElementById('view-note-title');
        const viewNoteContent = document.getElementById('view-note-content');
        const viewTaskValue = document.getElementById('view-task-value');
        const viewCategoryValue = document.getElementById('view-category-value');
        const viewDateValue = document.getElementById('view-date-value');
        const editBtn = document.getElementById('view-edit-btn');
        
        // Guardar o título padrão do modal
        const defaultTitle = viewNoteTitle.textContent;
        
        // Obter token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Fazer requisição para obter os detalhes da nota
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
                
                // Preencher dados no modal de visualização
                viewNoteTitle.textContent = note.title;
                viewNoteContent.textContent = note.content;
                viewDateValue.textContent = new Date(note.created_at).toLocaleString('pt-BR');
                
                // Informações da tarefa associada
                if (note.task && note.task_id) {
                    viewTaskValue.textContent = note.task.title;
                    document.getElementById('view-note-task').style.display = 'block';
                } else {
                    viewTaskValue.textContent = 'Nenhuma';
                    document.getElementById('view-note-task').style.display = 'block';
                }
                
                // Informações da categoria
                if (note.category) {
                    viewCategoryValue.innerHTML = `<span style="color: ${note.category.color}">${note.category.name}</span>`;
                    document.getElementById('view-note-category').style.display = 'block';
                } else {
                    viewCategoryValue.textContent = 'Nenhuma';
                    document.getElementById('view-note-category').style.display = 'block';
                }
                
                // Definir ID da nota no botão de edição
                if (editBtn) {
                    editBtn.setAttribute('data-id', note.id);
                }
                
                // Exibir o modal
                viewModal.style.display = 'block';
            } else {
                // Mostrar mensagem de erro
                Toast.fire({
                    icon: 'error',
                    title: 'Não foi possível carregar os dados da nota.'
                });
            }
        })
        .catch(error => {
            console.error('Erro ao buscar nota:', error);
            Toast.fire({
                icon: 'error',
                title: 'Erro ao carregar os dados da nota.'
            });
        });
    }
});

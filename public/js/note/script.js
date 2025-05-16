document.addEventListener('DOMContentLoaded', function() {
    // Inicializa os elementos do modal
    const noteModal = document.getElementById('noteModal');
    const noteIdInput = document.getElementById('noteId');
    const noteTitleInput = document.getElementById('noteTitle');
    const noteContentInput = document.getElementById('noteContent');
    const noteCategorySelect = document.getElementById('noteCategory');
    const noteTaskSelect = document.getElementById('noteTask');
    const noteModalLabel = document.getElementById('noteModalLabel');
    const deleteNoteBtn = document.getElementById('deleteNoteBtn');

    if (!noteModal || !noteIdInput || !noteTitleInput || !noteContentInput || !noteModalLabel) {
        console.error('Elementos do formulário não encontrados');
        return;
    }

    const modal = new bootstrap.Modal(noteModal);

    // Função para mostrar o modal de adicionar nota
    window.showAddNoteModal = function() {
        noteIdInput.value = '';
        noteTitleInput.value = '';
        noteContentInput.value = '';
        if (noteCategorySelect) noteCategorySelect.value = '';
        if (noteTaskSelect) noteTaskSelect.value = '';
        noteModalLabel.textContent = 'Nova Nota';
        if (deleteNoteBtn) deleteNoteBtn.style.display = 'none';
        modal.show();
    };

    // Função para visualizar nota
    window.viewNote = function(noteId) {
        // Prevenir propagação para evitar conflitos de evento
        event.stopPropagation();
        
        const noteCard = document.querySelector(`.note-card[data-note-id="${noteId}"]`);
        if (!noteCard) {
            console.error('Note card not found');
            return;
        }

        const titleElement = noteCard.querySelector('.note-title');
        
        // Buscar o conteúdo completo da nota via AJAX
        fetchNoteDetails(noteId);
        
        noteModalLabel.textContent = 'Visualizar Nota';
        if (deleteNoteBtn) deleteNoteBtn.style.display = 'block';
    };

    // Função para buscar detalhes completos da nota
    async function fetchNoteDetails(noteId) {
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                throw new Error('Token CSRF não encontrado');
            }

            noteIdInput.value = noteId;
            
            // Buscar a nota diretamente do card
            const noteCard = document.querySelector(`.note-card[data-note-id="${noteId}"]`);
            if (noteCard) {
                const titleElement = noteCard.querySelector('.note-title');
                const contentElement = noteCard.querySelector('.note-content');
                const taskLink = noteCard.querySelector('.task-link');
                const taskIcon = noteCard.querySelector('.note-indicators .fa-link');
                
                if (titleElement) noteTitleInput.value = titleElement.textContent.trim() || '';
                if (contentElement) {
                    // Obter o conteúdo truncado e remover os três pontos finais se existirem
                    let content = contentElement.textContent.trim() || '';
                    if (content.endsWith('...')) {
                        content = content.slice(0, -3); // Remove os três pontos finais
                    }
                    noteContentInput.value = content;
                }
                
                // Verificar se a nota tem uma tarefa associada
                if (noteTaskSelect) {
                    // Primeiro, limpar a seleção atual
                    noteTaskSelect.value = '';
                    
                    // Se tiver o ícone de tarefa ou o link da tarefa
                    if (taskIcon || taskLink) {
                        // Tentar obter o ID da tarefa diretamente do atributo data-task-id
                        const taskId = noteCard.getAttribute('data-task-id');
                        
                        if (taskId) {
                            // Definir o valor diretamente
                            noteTaskSelect.value = taskId;
                            // Acionar o evento change para mostrar as categorias da tarefa
                            const event = new Event('change');
                            noteTaskSelect.dispatchEvent(event);
                        } else {
                            // Fallback: tentar encontrar pelo nome da tarefa
                            const taskName = taskLink ? taskLink.textContent.replace('Vinculado à tarefa:', '').trim() : '';
                            if (taskName) {
                                Array.from(noteTaskSelect.options).forEach(option => {
                                    if (option.textContent.includes(taskName)) {
                                        noteTaskSelect.value = option.value;
                                        // Acionar o evento change para mostrar as categorias da tarefa
                                        const event = new Event('change');
                                        noteTaskSelect.dispatchEvent(event);
                                    }
                                });
                            }
                        }
                    }
                }
                
                modal.show();
            }
        } catch (error) {
            console.error('Erro ao buscar detalhes da nota:', error);
            alert('Ocorreu um erro ao abrir a nota. Por favor, tente novamente.');
        }
    }

    // Função para mostrar as categorias da tarefa selecionada
    window.showTaskCategories = function(taskId) {
        const taskCategoriesContainer = document.getElementById('taskCategoriesContainer');
        const taskCategoriesList = document.querySelector('.task-categories-list');
        
        if (!taskCategoriesContainer || !taskCategoriesList) return;
        
        // Limpar a lista de categorias
        taskCategoriesList.innerHTML = '';
        
        if (!taskId) {
            taskCategoriesContainer.style.display = 'none';
            return;
        }
        
        // Encontrar a opção selecionada
        const selectedOption = document.querySelector(`#noteTask option[value="${taskId}"]`);
        if (!selectedOption) {
            taskCategoriesContainer.style.display = 'none';
            return;
        }
        
        // Verificar se a tarefa tem categorias
        const hasCategories = selectedOption.getAttribute('data-has-categories') === 'true';
        
        if (!hasCategories) {
            taskCategoriesContainer.style.display = 'none';
            return;
        }
        
        // Buscar as categorias da tarefa via AJAX
        fetch(`/tasks/${taskId}/details`)
            .then(response => response.json())
            .then(task => {
                if (task.categories && task.categories.length > 0) {
                    taskCategoriesContainer.style.display = 'block';
                    
                    task.categories.forEach(category => {
                        const badge = document.createElement('span');
                        badge.className = 'category-badge';
                        badge.style.borderColor = category.color || '#6c757d';
                        badge.style.backgroundColor = `${category.color}20` || 'transparent';
                        badge.textContent = category.name;
                        taskCategoriesList.appendChild(badge);
                    });
                } else {
                    taskCategoriesContainer.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Erro ao buscar categorias da tarefa:', error);
                taskCategoriesContainer.style.display = 'none';
            });
    };
    
    // Função para salvar nota
    window.saveNote = async function() {
        const noteId = noteIdInput.value;
        const title = noteTitleInput.value;
        const content = noteContentInput.value;
        const taskId = noteTaskSelect ? noteTaskSelect.value : null;
        const isEdit = noteId !== '';

        if (!title || !content) {
            alert('Por favor, preencha o título e o conteúdo da nota.');
            return;
        }

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                throw new Error('Token CSRF não encontrado');
            }

            const response = await fetch(isEdit ? `/notes/${noteId}` : '/notes', {
                method: isEdit ? 'PUT' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.content
                },
                body: JSON.stringify({ 
                    title, 
                    content,
                    task_id: taskId
                })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Erro ao salvar a nota');
            }

            window.location.reload();
        } catch (error) {
            alert(error.message);
        }
    };

    // Função para deletar nota do modal
    window.deleteNoteFromModal = async function() {
        const noteId = noteIdInput.value;
        if (!noteId) return;
        
        if (!confirm('Tem certeza que deseja excluir esta nota?')) {
            return;
        }
        
        deleteNote(noteId);
    };

    // Função para deletar nota
    window.deleteNote = async function(noteId) {
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                throw new Error('Token CSRF não encontrado');
            }

            const response = await fetch(`/notes/${noteId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken.content
                }
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Erro ao excluir a nota');
            }

            window.location.reload();
        } catch (error) {
            alert(error.message);
        }
    };
});
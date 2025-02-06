@extends('layouts.app')

@section('content')

<div class="container">
    <div class="notes-section">
        <div class="dashboard-section-title mb-4 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="fas fa-sticky-note fa-2x me-3"></i>
                <h1 class="mb-0">Bloco de Notas</h1>
            </div>
            <div class="d-flex gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary back-button">
                <i class="fas fa-arrow-left me-2"></i>Voltar
            </a>
                <button class="btn btn-primary" id="addNoteBtn" onclick="showAddNoteModal()">
                    <i class="fas fa-plus me-1"></i>
                    Nova Nota
                </button>
            </div>
        </div>
        
        <div class="notes-container">
            <div class="row" id="notesContainer">
                @forelse ($notes as $note)
                    <div class="col-md-4 mb-3 note-card" data-note-id="{{ $note->id }}">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 note-title">{{ $note->title }}</h5>
                                <div class="note-actions">
                                    <button class="btn btn-sm btn-link text-primary" onclick="editNote({{ $note->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-link text-danger" onclick="deleteNote({{ $note->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="card-text note-content">{{ $note->content }}</p>
                            </div>
                            <div class="card-footer text-muted">
                                <small>Última atualização: {{ $note->updated_at->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle me-2"></i>
                            Você ainda não tem notas. Crie até 3 notas para organizar suas ideias!
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Modal para adicionar/editar nota -->
    <div class="modal fade" id="noteModal" tabindex="-1" aria-labelledby="noteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="noteModalLabel">Nova Nota</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="noteForm">
                        <input type="hidden" id="noteId">
                        <div class="mb-3">
                            <label for="noteTitle" class="form-label">Título</label>
                            <input type="text" class="form-control" id="noteTitle" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label for="noteContent" class="form-label">Conteúdo</label>
                            <textarea class="form-control" id="noteContent" rows="4" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="saveNote()">Salvar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializa os elementos do modal
        const noteModal = document.getElementById('noteModal');
        const noteIdInput = document.getElementById('noteId');
        const noteTitleInput = document.getElementById('noteTitle');
        const noteContentInput = document.getElementById('noteContent');
        const noteModalLabel = document.getElementById('noteModalLabel');

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
            noteModalLabel.textContent = 'Nova Nota';
            modal.show();
        };

        // Função para editar nota
        window.editNote = function(noteId) {
            const noteCard = document.querySelector(`.note-card[data-note-id="${noteId}"]`);
            if (!noteCard) {
                console.error('Note card not found');
                return;
            }

            const titleElement = noteCard.querySelector('.note-title');
            const contentElement = noteCard.querySelector('.note-content');
            
            if (!titleElement || !contentElement) {
                console.error('Note elements not found');
                return;
            }

            const title = titleElement.textContent || '';
            const content = contentElement.textContent || '';

            noteIdInput.value = noteId;
            noteTitleInput.value = title;
            noteContentInput.value = content;
            noteModalLabel.textContent = 'Editar Nota';
            modal.show();
        };

        // Função para salvar nota
        window.saveNote = async function() {
            const noteId = noteIdInput.value;
            const title = noteTitleInput.value;
            const content = noteContentInput.value;
            const isEdit = noteId !== '';

            if (!title || !content) {
                alert('Por favor, preencha todos os campos.');
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
                    body: JSON.stringify({ title, content })
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

        // Função para deletar nota
        window.deleteNote = async function(noteId) {
            if (!confirm('Tem certeza que deseja excluir esta nota?')) {
                return;
            }

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
</script>
@endsection

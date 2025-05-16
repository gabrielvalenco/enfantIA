@extends('layouts.app')

@section('content')

<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<link rel="stylesheet" href="{{ asset('css/notes-style.css') }}">

<div class="container">
    <div class="notes-section">
        <div class="dashboard-section-title d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="fas fa-sticky-note fa-2x me-3"></i>
                <h1 class="mb-0">Bloco de Notas</h1>
            </div>
            <div class="d-flex gap-3">
                <a href="{{ route('dashboard') }}">
                   Voltar
                </a>
                <button class="add-button" id="addNoteBtn" onclick="showAddNoteModal()">
                    <i class="fas fa-plus-circle"></i> Nova Nota
                </button>
            </div>
        </div>
        
        <div class="notes-container">
            <div class="row" id="notesContainer">
                @forelse ($notes as $note)
                    <div class="col-md-4 mb-3 note-card" data-note-id="{{ $note->id }}" data-task-id="{{ $note->task_id }}" onclick="viewNote({{ $note->id }})">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0 note-title">{{ $note->title }}</h5>
                                @if($note->task && $note->task->categories && $note->task->categories->count() > 0)
                                    <div class="category-badges">
                                        @foreach($note->task->categories as $category)
                                            <span class="category-badge" style="border-color: {{ $category->color ?? '#6c757d' }}; background-color: {{ $category->color }}20;">
                                                {{ $category->name ?? 'Categoria' }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="card-body">
                                <p class="card-text note-content">
                                    {{ Str::limit($note->content, 100, '...') }}
                                </p>
                                @if($note->task_id)
                                    <div class="task-link">
                                        <i class="fas fa-tasks me-1"></i>
                                        <small>Vinculado à tarefa: {{ $note->task->title ?? 'Tarefa' }}</small>
                                    </div>
                                @endif
                            </div>
                            <div class="card-footer text-muted d-flex justify-content-between align-items-center">
                                <small>{{ $note->updated_at->format('d/m/Y H:i') }}</small>
                                <div class="note-indicators">
                                    @if($note->task && $note->task->categories && $note->task->categories->count() > 0)
                                        <i class="fas fa-tags" title="Categorias da tarefa"></i>
                                    @endif
                                    @if($note->task_id)
                                        <i class="fas fa-link ms-2" title="Vinculado à tarefa"></i>
                                    @endif
                                </div>
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

    <!-- Modal para visualizar/editar nota -->
    <div class="modal fade" id="noteModal" tabindex="-1" aria-labelledby="noteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="noteModalLabel">Visualizar Nota</h5>
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
                            <textarea class="form-control" id="noteContent" rows="6" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="noteTask" class="form-label">Vincular à tarefa (opcional)</label>
                                <select class="form-control" id="noteTask" onchange="showTaskCategories(this.value)">
                                    <option value="">Sem vínculo</option>
                                    @php
                                        $tasks = Auth::user()->tasks()->with('categories')->where('status', 0)->get();
                                    @endphp
                                    @if($tasks && $tasks->count() > 0)
                                        @foreach($tasks as $task)
                                            <option value="{{ $task->id }}" data-has-categories="{{ $task->categories->count() > 0 ? 'true' : 'false' }}">
                                                {{ $task->title }}
                                                @if($task->categories->count() > 0)
                                                    ({{ $task->categories->count() }} {{ $task->categories->count() == 1 ? 'categoria' : 'categorias' }})
                                                @endif
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div id="taskCategoriesContainer" class="mb-3" style="display: none;">
                            <label class="form-label">Categorias da tarefa:</label>
                            <div class="task-categories-list">
                                <!-- As categorias da tarefa serão exibidas aqui -->
                            </div>
                            <small class="text-muted mt-2">As categorias são herdadas automaticamente da tarefa selecionada.</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="d-flex justify-content-between w-100">
                        <button type="button" class="btn btn-danger" id="deleteNoteBtn" onclick="deleteNoteFromModal()"><i class="fas fa-trash me-1"></i> Excluir</button>
                        <div>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                            <button type="button" class="btn btn-primary" onclick="saveNote()"><i class="fas fa-save me-1"></i> Salvar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/note/script.js') }}"></script>
@endsection

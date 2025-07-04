<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('APP_NAME') }} - Notas</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notes/note.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body>
    <div class="container">
        <div class="table-header">
            <h1>Notas</h1>
            <div class="table-actions">
                <a class="back-button" href="{{ route('dashboard') }}">
                    <span class="back-text">Voltar ao Dashboard</span>
                    <i class="fas fa-sign-out-alt mobile-icon"></i>
                </a>
                <button class="add-button" id="open-note-modal">
                    <i class="fas fa-plus-circle"></i>
                    Nova Nota
                </button>
            </div>
        </div>
        
        <div class="notes-container">
            @if(isset($notes) && count($notes) > 0)
                @foreach($notes as $note)
                <div class="note-card" data-id="{{ $note->id }}">
                    <div class="note-header">
                        <h3>{{ $note->title }}</h3>
                        <div class="note-actions">
                            <button class="note-edit" data-id="{{ $note->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="note-delete" data-id="{{ $note->id }}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="note-content">{{ 
                        Str::length($note->content) > 150 ? Str::limit($note->content, 150, '...') : $note->content 
                    }}</div>
                    <div class="note-footer">
                        <div class="note-references">
                            @if($note->task_id)
                                <div class="note-reference task-reference">
                                    <i class="fas fa-tasks"></i>
                                    <span>{{ $note->task->title }}</span>
                                </div>
                            @endif
                            @if($note->category_id)
                                <div class="note-reference category-reference">
                                    <i class="fas fa-tag" style="color: {{ $note->category->color }};"></i>
                                    <span>{{ $note->category->name }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="note-date">{{ $note->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="no-notes">
                    <i class="far fa-sticky-note"></i>
                    <p>Você ainda não tem notas. Crie uma nova nota para começar!</p>
                </div>
            @endif
        </div>
        
        <!-- Modal para criar/editar notas -->
        <div class="modal" id="note-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 id="modal-title">Nova Nota</h2>
                    <span class="close-modal">&times;</span>
                </div>
                <div class="modal-body">
                    <form id="note-form" method="POST" action="{{ route('notes.store') }}">
                        @csrf
                        <input type="hidden" id="note-id" name="id">
                        
                        <div class="form-group">
                            <label for="note-title">Título</label>
                            <input type="text" id="note-title" name="title" maxlength="90" required>
                            <div class="char-counter"><span id="title-counter">0</span>/90</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="note-content">Descrição</label>
                            <textarea id="note-content" name="content" rows="5" maxlength="1000" required></textarea>
                            <div class="char-counter"><span id="content-counter">0</span>/1000</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="task-id">Tarefa relacionada (opcional)</label>
                            <select id="task-id" name="task_id">
                                <option value="">Selecione uma tarefa</option>
                                @if(isset($tasks) && count($tasks) > 0)
                                    @foreach($tasks as $task)
                                        <option value="{{ $task->id }}">{{ $task->title }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="category-id">Categoria relacionada (opcional)</label>
                            <select id="category-id" name="category_id">
                                <option value="">Selecione uma categoria</option>
                                @if(isset($categories) && count($categories) > 0)
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="category-id">Categoria (opcional)</label>
                            <select id="category-id" name="category_id">
                                <option value="">Selecione uma categoria</option>
                                @if(isset($categories) && count($categories) > 0)
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" style="color: {{ $category->color }}">
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="save-button">Salvar</button>
                            <button type="button" class="cancel-button" id="cancel-note">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <button id="theme-toggle" class="theme-toggle" aria-label="Toggle dark/light mode">
        <i class="fas fa-moon"></i>
    </button>
    
    <script src="{{ asset('js/script.js') }}"></script>
    <script src="{{ asset('js/notes/index.js') }}"></script>
    <script>
        // Variáveis globais para o SweetAlert
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
    </script>
</body>
</html>
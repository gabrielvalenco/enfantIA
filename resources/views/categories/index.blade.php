<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ env('APP_NAME') }} - Categorias</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/category-style.css') }}">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
</head>
<body>
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        <div class="table-header">
            <h1>Categorias</h1>
            <div class="table-actions">
                <a class="back-button" href="{{ route('dashboard') }}">
                    <span class="back-text">Voltar ao Dashboard</span>
                    <i class="fas fa-sign-out-alt mobile-icon"></i>
                </a>
                <a class="add-button" href="#">
                    <i class="fas fa-plus-circle"></i> Nova Categoria
                </a>
            </div>
        </div>
        
        <table class="table">
            <thead>
                <tr>
                    <th id="name">Nome</th>
                    <th id="description">Descrição</th>
                    <th id="actions">Ações</th>
                </tr>
            </thead>
            <tbody>
                @if($categories->isEmpty())
                    <tr>
                        <td colspan="3" class="text-center">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle mr-2"></i>
                                Não há categorias cadastradas no momento.
                            </div>
                        </td>
                    </tr>
                @else
                    @foreach($categories as $category)
                    <tr>
                        <td>
                            <div class="category-name-container">
                                <span class="color-preview" style="background-color: {{ $category->color }};"></span>
                                <span class="category-name">{{ $category->name }}</span>
                            </div>
                        </td>
                        <td>{{ Str::limit($category->description, 50, ' [...]') }}</td>
                        <td>
                            <div class="action-buttons">
                                <button type="button" class="badge edit-badge" data-category-id="{{ $category->id }}" data-category-description="{{ $category->description }}">Editar</button>
                                <form class="d-inline delete-category-form" action="{{ route('categories.destroy', $category->id) }}" method="POST" style="background-color: transparent;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="badge delete-badge" data-category-id="{{ $category->id }}" data-category-name="{{ $category->name }}">Excluir</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <!-- Modal para Editar Categoria -->
    <div id="editCategoryModal" class="modal" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title">Editar Categoria</h3>
            </div>
            <div class="modal-body">
                <form id="edit-category-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit-category-id">
                    <div class="form-group">
                        <label for="edit-name">Nome da Categoria</label>
                        <input type="text" name="name" id="edit-name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-description">Descrição <small class="text-muted">(opcional)</small></label>
                        <textarea name="description" class="form-control" id="edit-description" rows="3"></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label>Cor da Categoria</label>
                        <input type="color" name="color" id="edit-color" class="form-control" style="width: 100%; height: 40px; padding: 2px;">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="cancel-button" onclick="closeModal('editCategoryModal')">Cancelar</button>
                <button type="button" class="add-button" id="save-edit-category-btn">
                    <i class="fas fa-save"></i> Salvar
                </button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/category/script.js') }}"></script>
    <script src="{{ asset('js/script.js') }}"></script>

    <!-- Modal para Criar Categoria -->
    <div id="createCategoryModal" class="modal" style="display: none;">
        <div class="modal-dialog">
                <div class="modal-header">
                    <h3 class="modal-title">Nova Categoria</h3>
                </div>
                <div class="modal-body">
                    <form id="create-category-form">
                        @csrf
                        <div class="form-group">
                            <label for="create-name">Nome da Categoria</label>
                            <input type="text" name="name" id="create-name" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="create-description">Descrição <small class="text-muted">(opcional)</small></label>
                            <textarea name="description" class="form-control" id="create-description" rows="3"></textarea>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label>Cor da Categoria</label>
                            <input type="color" name="color" id="edit-color" class="form-control" style="width: 100%; height: 40px; padding: 2px;">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="cancel-button" onclick="closeModal('createCategoryModal')">Cancelar</button>
                    <button type="button" class="add-button" id="save-create-category-btn">
                        <i class="fas fa-save"></i> Criar Categoria
                    </button>
                </div>
        </div>
    </div>
</body>

<button id="theme-toggle" class="theme-toggle" aria-label="Toggle dark/light mode">
    <i class="fas fa-moon"></i>
</button>

</html>

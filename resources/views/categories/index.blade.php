<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Categorias</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/task/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/category-style.css') }}">
</head>
<body>
    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        <div class="table-header">
            <h1>Categorias</h1>
            <div class="table-actions">
                <a class="back-button" href="{{ route('dashboard') }}">
                    Voltar ao Dashboard
                </a>
                <a class="add-task-button add-category-button" href="#">
                    <i class="fas fa-plus-circle"></i> Nova Categoria
                </a>
            </div>
        </div>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th class="actions-column">Ações</th>
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
                            <div class="d-flex align-items-center">
                                <span class="color-preview" style="background-color: {{ $category->color }};"></span>
                                {{ $category->name }}
                            </div>
                        </td>
                        <td>{{ Str::limit($category->description, 50, ' [...]') }}</td>
                        <td>
                            <div class="action-buttons">
                                <button type="button" class="btn btn-warning btn-sm edit-category-btn" data-category-id="{{ $category->id }}">Editar</button>
                                <form class="d-inline delete-category-form" action="{{ route('categories.destroy', $category->id) }}" method="POST" style="background-color: transparent;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm delete-category-btn" data-category-id="{{ $category->id }}" data-category-name="{{ $category->name }}">Excluir</button>
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
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalLabel">Editar Categoria</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="edit-category-form">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit-category-id">
                        
                        <div class="form-group">
                            <label for="edit-name" class="font-weight-bold">Nome da Categoria</label>
                            <input type="text" name="name" class="form-control" id="edit-name" required>
                        </div>

                        <div class="form-group">
                            <label for="edit-description" class="font-weight-bold">Descrição <small class="text-muted">(opcional)</small></label>
                            <textarea name="description" class="form-control" id="edit-description" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="edit-color" class="font-weight-bold">Cor da Categoria</label>
                            <input type="color" name="color" id="edit-color" class="form-control">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="save-edit-category-btn">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/category/script.js') }}"></script>
    <!-- Modal para Criar Categoria -->
    <div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createCategoryModalLabel">Nova Categoria</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
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
                            <input type="color" name="color" id="create-color" class="form-control" value="#DC3545">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" form="create-category-form" class="btn btn-primary">
                        <i class="fas fa-save"></i> Criar Categoria
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/script.js') }}"></script>
    
</body>

<button id="theme-toggle" class="theme-toggle" aria-label="Toggle dark/light mode">
    <i class="fas fa-moon"></i>
</button>

</html>

@extends('layouts.app')

@php
    $hideFooter = true;
@endphp

@section('content')

<link rel="stylesheet" href="{{ asset('css/task-form.css') }}">

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h1 class="mb-2 mb-md-0">Tarefas Concluídas</h1>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary back-button">Voltar</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive m-0">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Categorias</th>
                    <th>Data de Conclusão</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @if($tasks->isEmpty())
                    <tr>
                        <td colspan="4" class="text-center">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle mr-2"></i>
                                Não há tarefas concluídas no momento.
                            </div>
                        </td>
                    </tr>
                @else
                    @foreach($tasks as $task)
                        <tr>
                            <td>{{ $task->title }}</td>
                            <td>
                                @foreach($task->categories as $category)
                                    <span class="badge badge-info">{{ $category->name }}</span>
                                @endforeach
                            </td>
                            <td>{{ $task->updated_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="action-buttons">
                                    <form action="{{ route('tasks.uncomplete', $task) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-warning action-btn">
                                            <span class="action-text">Desfazer</span>
                                            <i class="fas fa-undo action-icon"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger action-btn" onclick="return confirm('Tem certeza?')">
                                            <span class="action-text">Excluir</span>
                                            <i class="fas fa-trash action-icon"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
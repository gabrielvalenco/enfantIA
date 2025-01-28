@extends('layouts.app')

@section('content')
<header class="dashboard-header">
    <h1 class="dashboard-title">Painel de Controle</h1>
    <div class="user-icon">
        <i class="fas fa-user"></i>
    </div>
</header>

<div class="dashboard-menu">
    <a href="{{ route('tasks.create') }}" class="menu-item">
        <i class="icon-plus"></i>
        Criar Tarefa
    </a>

    <a href="{{ route('tasks.index') }}" class="menu-item">
        <i class="icon-list"></i>
        Verificar Tarefas Pendentes
    </a>

    <a href="{{ route('tasks.completed') }}" class="menu-item">
        <i class="icon-check"></i>
        Conferir Tarefas Concluídas
    </a>

    <a href="{{ route('categories.index') }}" class="menu-item">
        <i class="icon-tag"></i>
        Categorias
    </a>
</div>
@endsection

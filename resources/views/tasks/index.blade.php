<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task List</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/custom-styles.css') }}">
</head>
<body class="bg-light">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="table-header">
            <h1 class="table-title">Task List</h1>
            <div class="table-actions">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Voltar</a>
                <a href="{{ route('tasks.create') }}" class="btn btn-primary">Add New Task</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Categories</th>
                        <th>Urgency</th>
                        <th>Status</th>
                        <th>Due Date</th>
                        <th class="actions-column">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $task)
                    <tr>
                        <td class="task-title">{{ $task->title }}</td>
                        <td class="task-description">{{ $task->description }}</td>
                        <td class="categories-column">
                            @foreach($task->categories as $category)
                                <span class="badge badge-info">{{ $category->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            <span class="badge {{ $task->urgency == 'high' ? 'badge-danger' : ($task->urgency == 'medium' ? 'badge-warning' : 'badge-success') }}">
                                {{ ucfirst($task->urgency) }}
                            </span>
                        </td>
                        <td>
                            <form action="{{ route('tasks.complete', $task) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $task->status ? 'btn-success' : 'btn-secondary' }}">
                                    {{ $task->status ? 'Completed' : 'Pending' }}
                                </button>
                            </form>
                        </td>
                        <td class="due-date">{{ $task->due_date }}</td>
                        <td class="actions-column">
                            <div class="action-buttons">
                                <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-warning action-btn">
                                    <span class="action-text">Edit</span>
                                    <i class="fas fa-edit action-icon"></i>
                                </a>
                                <form action="{{ route('tasks.destroy', $task) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger action-btn" onclick="return confirm('Are you sure?')">
                                        <span class="action-text">Delete</span>
                                        <i class="fas fa-trash action-icon"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

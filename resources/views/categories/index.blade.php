<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category List</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Category List</h1>
        <a href="{{ route('categories.create') }}" class="btn btn-primary mb-3">Add New Category</a>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                <tr>
                    <td>{{ $category->name }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>

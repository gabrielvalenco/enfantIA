<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $messages = [
            'name.required' => 'O nome da categoria é obrigatório.',
            'name.unique' => 'Já existe uma categoria com este nome.',
            'description.required' => 'A descrição da categoria é obrigatória.'
        ];

        $request->validate([
            'name' => 'required|unique:categories',
            'description' => 'required'
        ], $messages);

        Category::create($request->all());
        return redirect()->route('categories.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $messages = [
            'name.required' => 'O nome da categoria é obrigatório.',
            'name.unique' => 'Já existe uma categoria com este nome.',
            'description.required' => 'A descrição da categoria é obrigatória.'
        ];

        $request->validate([
            'name' => 'required|unique:categories,name,' . $category->id,
            'description' => 'required'
        ], $messages);

        $category->update($request->all());
        return redirect()->route('categories.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index')
            ->with('success', 'Categoria excluída com sucesso!');
    }
}

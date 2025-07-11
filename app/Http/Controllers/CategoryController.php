<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('user_id', auth()->id())->get();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,NULL,id,user_id,' . auth()->id(),
            'description' => 'nullable|string',
            'color' => 'required|string|max:7'
        ]);

        $category = new Category([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
            'user_id' => auth()->id()
        ]);

        $category->save();

        return redirect()->route('categories.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    public function edit(Category $category)
    {
        $this->authorize('update', $category);
        return response()->json($category);
    }

    public function update(Request $request, Category $category)
    {
        $this->authorize('update', $category);
        
        $messages = [
            'name.required' => 'O nome da categoria é obrigatório.',
            'name.unique' => 'Já existe uma categoria com este nome.',
        ];

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id . ',id,user_id,' . auth()->id(),
            'description' => 'nullable|string',
            'color' => 'required|string|max:7'
        ], $messages);

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color
        ]);
        
        // Check if the request is AJAX
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Categoria atualizada com sucesso!',
                'category' => $category
            ]);
        }
        
        // For non-AJAX requests, redirect
        return redirect()->route('categories.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);
        $category->delete();
        return redirect()->route('categories.index')
            ->with('success', 'Categoria excluída com sucesso!');
    }
}

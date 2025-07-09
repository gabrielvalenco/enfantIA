<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Services\UserActivityLogService;

class CategoryController extends Controller
{
    protected $activityLogService;
    
    /**
     * Create a new controller instance.
     */
    public function __construct(UserActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }
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
        $messages = [
            'name.required' => 'O nome da categoria é obrigatório.',
            'name.unique' => 'Já existe uma categoria com este nome.',
            'color.required' => 'A cor da categoria é obrigatória.'
        ];
        
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,NULL,id,user_id,' . auth()->id(),
            'description' => 'nullable|string',
            'color' => 'required|string|max:7'
        ], $messages);

        $category = new Category([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
            'user_id' => auth()->id()
        ]);

        $category->save();
        
        // Log category creation activity
        $this->activityLogService->log(
            'create',
            "Criou categoria '{$category->name}'",
            'category',
            $category->id
        );
        
        // Check if the request is AJAX
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Categoria criada com sucesso!',
                'category' => $category
            ]);
        }

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
        
        // Log category update activity
        $this->activityLogService->log(
            'update',
            "Atualizou categoria '{$category->name}'",
            'category',
            $category->id
        );
        
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
        
        // Store category name before deletion
        $categoryName = $category->name;
        $categoryId = $category->id;
        
        $category->delete();
        
        // Log category deletion activity
        $this->activityLogService->log(
            'delete',
            "Excluiu categoria '{$categoryName}'",
            'category',
            $categoryId
        );
        
        return redirect()->route('categories.index')
            ->with('success', 'Categoria excluída com sucesso!');
    }
}

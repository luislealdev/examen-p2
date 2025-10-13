<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Category::query();

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Sort functionality
        $sortBy = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        
        if (in_array($sortBy, ['name', 'last_update', 'category_id'])) {
            if ($sortBy === 'name') {
                $query->alphabetical();
                if ($sortDirection === 'desc') {
                    $query->reorder('name', 'desc');
                }
            } else {
                $query->orderBy($sortBy, $sortDirection);
            }
        } else {
            $query->alphabetical(); // Default sorting
        }

        // Pagination with request parameters preserved
        $categories = $query->paginate(20)->withQueryString();

        // Get statistics
        $totalCategories = Category::count();
        $recentlyAdded = Category::recentlyUpdated()->count();

        return view('categories.index', compact('categories', 'totalCategories', 'recentlyAdded'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:25|unique:category,name',
        ], [
            'name.required' => 'El nombre de la categoría es obligatorio.',
            'name.string' => 'El nombre de la categoría debe ser texto.',
            'name.max' => 'El nombre de la categoría no puede exceder los 25 caracteres.',
            'name.unique' => 'Ya existe una categoría con este nombre.',
        ]);

        // Clean and format the name
        $validated['name'] = trim($validated['name']);

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', "¡Categoría '{$validated['name']}' creada exitosamente!");
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category): View
    {
        // Load related data when available
        // $category->load(['films']);
        
        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:25|unique:category,name,' . $category->category_id . ',category_id',
        ], [
            'name.required' => 'El nombre de la categoría es obligatorio.',
            'name.string' => 'El nombre de la categoría debe ser texto.',
            'name.max' => 'El nombre de la categoría no puede exceder los 25 caracteres.',
            'name.unique' => 'Ya existe una categoría con este nombre.',
        ]);

        // Clean and format the name
        $validated['name'] = trim($validated['name']);

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', "¡Categoría actualizada a '{$validated['name']}' exitosamente!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        // Check if category is in use (when Film model exists)
        // if ($category->films()->exists()) {
        //     return redirect()->route('categories.index')
        //         ->with('error', 'No se puede eliminar una categoría que está asignada a películas.');
        // }

        $categoryName = $category->name;
        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', "¡Categoría '{$categoryName}' eliminada exitosamente!");
    }

    /**
     * Display categories grouped by first letter.
     */
    public function alphabetical(): View
    {
        $categoryGroups = Category::alphabetical()
            ->get()
            ->groupBy('first_letter');

        return view('categories.alphabetical', compact('categoryGroups'));
    }

    /**
     * Display popular categories (when films relationship exists).
     */
    public function popular(): View
    {
        // This will be useful when Film model exists
        $categories = Category::alphabetical()
            // ->withCount('films')
            // ->orderBy('films_count', 'desc')
            ->paginate(20);

        return view('categories.popular', compact('categories'));
    }
}

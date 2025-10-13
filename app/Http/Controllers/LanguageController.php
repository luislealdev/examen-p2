<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class LanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Language::query();

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Sort functionality
        $sortBy = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        
        if (in_array($sortBy, ['name', 'last_update', 'language_id'])) {
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
        $languages = $query->paginate(20)->withQueryString();

        // Get statistics
        $totalLanguages = Language::count();
        $recentlyAdded = Language::where('last_update', '>=', now()->subDays(30))->count();

        return view('languages.index', compact('languages', 'totalLanguages', 'recentlyAdded'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('languages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:20|unique:language,name',
        ]);

        // Clean and format the name
        $validated['name'] = trim($validated['name']);

        Language::create($validated);

        return redirect()->route('languages.index')
            ->with('success', "Language '{$validated['name']}' created successfully!");
    }

    /**
     * Display the specified resource.
     */
    public function show(Language $language): View
    {
        // Load related data when available
        // $language->load(['films', 'originalLanguageFilms']);
        
        return view('languages.show', compact('language'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Language $language): View
    {
        return view('languages.edit', compact('language'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Language $language): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:20|unique:language,name,' . $language->language_id . ',language_id',
        ]);

        // Clean and format the name
        $validated['name'] = trim($validated['name']);

        $language->update($validated);

        return redirect()->route('languages.index')
            ->with('success', "Language updated to '{$validated['name']}' successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Language $language): RedirectResponse
    {
        // Check if language is in use (when Film model exists)
        // if ($language->is_used) {
        //     return redirect()->route('languages.index')
        //         ->with('error', 'Cannot delete language that is currently in use by films.');
        // }

        $languageName = $language->name;
        $language->delete();

        return redirect()->route('languages.index')
            ->with('success', "Language '{$languageName}' deleted successfully!");
    }

    /**
     * Display languages grouped by first letter.
     */
    public function alphabetical(): View
    {
        $languageGroups = Language::alphabetical()
            ->get()
            ->groupBy('first_letter');

        return view('languages.alphabetical', compact('languageGroups'));
    }
}

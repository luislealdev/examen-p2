<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Language;
use App\Models\Category;
use App\Services\BusinessActivityLogger;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class FilmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Film::with(['language', 'originalLanguage', 'category'])
                    ->withCount([
                        'inventory',
                        'inventory as available_inventory_count' => function ($query) {
                            $query->available();
                        }
                    ]);

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->byRating($request->rating);
        }

        // Filter by language
        if ($request->filled('language_id')) {
            $query->byLanguage($request->language_id);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->byCategory($request->category_id);
        }

        // Filter by release year
        if ($request->filled('release_year')) {
            $query->byYear($request->release_year);
        }

        // Filter by rental rate range
        if ($request->filled('rental_rate_min') || $request->filled('rental_rate_max')) {
            $query->byRentalRate($request->rental_rate_min, $request->rental_rate_max);
        }

        // Filter by length range
        if ($request->filled('length_min') || $request->filled('length_max')) {
            $query->byLength($request->length_min, $request->length_max);
        }

        // Filter by special features
        if ($request->filled('has_special_features')) {
            $query->withSpecialFeatures();
        }

        // Filter by availability
        if ($request->filled('availability')) {
            $query->byAvailability($request->availability);
        }

        // Sorting
        $sortBy = $request->get('sort', 'title');
        $sortDirection = $request->get('direction', 'asc');
        
        switch ($sortBy) {
            case 'title':
                $query->alphabetical();
                if ($sortDirection === 'desc') {
                    $query->reorder('title', 'desc');
                }
                break;
            case 'release_year':
                $query->orderBy('release_year', $sortDirection);
                break;
            case 'rental_rate':
                $query->orderBy('rental_rate', $sortDirection);
                break;
            case 'length':
                $query->orderBy('length', $sortDirection);
                break;
            case 'rating':
                $query->orderBy('rating', $sortDirection);
                break;
            default:
                $query->alphabetical();
        }

        // Pagination with request parameters preserved
        $films = $query->paginate(20)->withQueryString();

        // Get filter data
        $languages = Language::alphabetical()->get();
        $categories = Category::alphabetical()->get();
        $ratings = Film::RATINGS;
        $years = Film::select('release_year')
            ->whereNotNull('release_year')
            ->distinct()
            ->orderBy('release_year', 'desc')
            ->pluck('release_year');

        // Get statistics
        $totalFilms = Film::count();
        $recentFilms = Film::recent()->count();
        $avgRentalRate = Film::avg('rental_rate');
        $avgLength = Film::avg('length');

        return view('films.index', compact(
            'films', 'languages', 'categories', 'ratings', 'years',
            'totalFilms', 'recentFilms', 'avgRentalRate', 'avgLength'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $languages = Language::alphabetical()->get();
        $categories = Category::alphabetical()->get();
        $ratings = Film::RATINGS;
        $specialFeatures = Film::SPECIAL_FEATURES;

        return view('films.create', compact('languages', 'categories', 'ratings', 'specialFeatures'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:128',
            'description' => 'nullable|string',
            'release_year' => 'nullable|integer|min:1888|max:' . (now()->year + 5),
            'language_id' => 'required|exists:language,language_id',
            'original_language_id' => 'nullable|exists:language,language_id',
            'rental_duration' => 'required|integer|min:1|max:30',
            'rental_rate' => 'required|numeric|min:0|max:99.99',
            'length' => 'nullable|integer|min:1|max:1000',
            'replacement_cost' => 'required|numeric|min:0|max:999.99',
            'rating' => ['required', Rule::in(Film::RATINGS)],
            'special_features' => 'nullable|array',
            'special_features.*' => Rule::in(Film::SPECIAL_FEATURES),
            'category_id' => 'nullable|exists:category,category_id',
            'poster_url' => 'nullable|url|max:500',
        ]);

        // Clean and format data
        $validated['title'] = trim($validated['title']);
        $validated['description'] = $validated['description'] ? trim($validated['description']) : null;
        $validated['poster_url'] = $validated['poster_url'] ? trim($validated['poster_url']) : null;

        // Create the film
        $film = Film::create($validated);

        return redirect()->route('films.index')
            ->with('success', "Film '{$validated['title']}' created successfully!");
    }

    /**
     * Display the specified resource.
     */
    public function show(Film $film): View
    {
        $film->load(['language', 'originalLanguage', 'category']);
        
        // Log film viewing activity
        BusinessActivityLogger::logFilm('view', $film->film_id, [
            'film_title' => $film->title,
            'category' => $film->category->name ?? null,
            'rating' => $film->rating,
        ]);
        
        return view('films.show', compact('film'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Film $film): View
    {
        $film->load(['category']);
        $languages = Language::alphabetical()->get();
        $categories = Category::alphabetical()->get();
        $ratings = Film::RATINGS;
        $specialFeatures = Film::SPECIAL_FEATURES;

        return view('films.edit', compact('film', 'languages', 'categories', 'ratings', 'specialFeatures'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Film $film): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:128',
            'description' => 'nullable|string',
            'release_year' => 'nullable|integer|min:1888|max:' . (now()->year + 5),
            'language_id' => 'required|exists:language,language_id',
            'original_language_id' => 'nullable|exists:language,language_id',
            'rental_duration' => 'required|integer|min:1|max:30',
            'rental_rate' => 'required|numeric|min:0|max:99.99',
            'length' => 'nullable|integer|min:1|max:1000',
            'replacement_cost' => 'required|numeric|min:0|max:999.99',
            'rating' => ['required', Rule::in(Film::RATINGS)],
            'special_features' => 'nullable|array',
            'special_features.*' => Rule::in(Film::SPECIAL_FEATURES),
            'category_id' => 'nullable|exists:category,category_id',
            'poster_url' => 'nullable|url|max:500',
        ]);

        // Clean and format data
        $validated['title'] = trim($validated['title']);
        $validated['description'] = $validated['description'] ? trim($validated['description']) : null;
        $validated['poster_url'] = $validated['poster_url'] ? trim($validated['poster_url']) : null;

        // Update the film
        $film->update($validated);

        return redirect()->route('films.index')
            ->with('success', "Film '{$validated['title']}' updated successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Film $film): RedirectResponse
    {
        $filmTitle = $film->title;
        
        $film->delete();

        return redirect()->route('films.index')
            ->with('success', "Film '{$filmTitle}' deleted successfully!");
    }

    /**
     * Display films by category.
     */
    public function byCategory(Category $category): View
    {
        $films = Film::with(['language', 'originalLanguage', 'category'])
            ->byCategory($category->category_id)
            ->alphabetical()
            ->paginate(20);

        return view('films.by-category', compact('films', 'category'));
    }

    /**
     * Display films by language.
     */
    public function byLanguage(Language $language): View
    {
        $films = Film::with(['language', 'originalLanguage', 'categories'])
            ->byLanguage($language->language_id)
            ->alphabetical()
            ->paginate(20);

        return view('films.by-language', compact('films', 'language'));
    }

    /**
     * Display films by rating.
     */
    public function byRating(string $rating): View
    {
        if (!in_array($rating, Film::RATINGS)) {
            abort(404);
        }

        $films = Film::with(['language', 'originalLanguage', 'categories'])
            ->byRating($rating)
            ->alphabetical()
            ->paginate(20);

        return view('films.by-rating', compact('films', 'rating'));
    }

    /**
     * Display films by decade.
     */
    public function byDecade(int $decade): View
    {
        $films = Film::with(['language', 'originalLanguage', 'categories'])
            ->whereBetween('release_year', [$decade, $decade + 9])
            ->newest()
            ->paginate(20);

        return view('films.by-decade', compact('films', 'decade'));
    }

    /**
     * Display recent films.
     */
    public function recent(): View
    {
        $films = Film::with(['language', 'originalLanguage', 'categories'])
            ->recent()
            ->newest()
            ->paginate(20);

        return view('films.recent', compact('films'));
    }

    /**
     * Display film statistics.
     */
    public function statistics(): View
    {
        $stats = [
            'total_films' => Film::count(),
            'by_rating' => Film::selectRaw('rating, COUNT(*) as count')
                ->groupBy('rating')
                ->pluck('count', 'rating'),
            'by_language' => Film::with('language')
                ->selectRaw('language_id, COUNT(*) as count')
                ->groupBy('language_id')
                ->get()
                ->mapWithKeys(fn($item) => [$item->language->name ?? 'Unknown' => $item->count]),
            'by_decade' => Film::selectRaw('FLOOR(release_year/10)*10 as decade, COUNT(*) as count')
                ->whereNotNull('release_year')
                ->groupBy('decade')
                ->orderBy('decade', 'desc')
                ->pluck('count', 'decade'),
            'avg_rental_rate' => Film::avg('rental_rate'),
            'avg_length' => Film::avg('length'),
            'avg_replacement_cost' => Film::avg('replacement_cost'),
            'recent_films' => Film::recent()->count(),
            'with_special_features' => Film::withSpecialFeatures()->count(),
        ];

        return view('films.statistics', compact('stats'));
    }
}

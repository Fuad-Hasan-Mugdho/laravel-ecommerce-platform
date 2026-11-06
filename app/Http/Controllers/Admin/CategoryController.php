<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::query()
            ->withCount(['subcategories', 'products'])
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%'.$request->string('q').'%'))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create', ['category' => new Category(['is_active' => true])]);
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $category = Category::create($request->validated());

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "Category \"{$category->name}\" created.");
    }

    public function show(Category $category): View
    {
        $category->load(['subcategories' => fn ($q) => $q->withCount('products')])
            ->loadCount('products');

        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "Category \"{$category->name}\" updated.");
    }

    public function destroy(Category $category): RedirectResponse
    {
        $name = $category->name;

        // Deleting a category removes its subcategories and products
        // (handled by model events so image files are cleaned up too).
        DB::transaction(fn () => $category->delete());

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "Category \"{$name}\" and all of its subcategories and products were deleted.");
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SubcategoryRequest;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SubcategoryController extends Controller
{
    public function index(Request $request): View
    {
        $subcategories = Subcategory::query()
            ->with('category')
            ->withCount('products')
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->integer('category')))
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%'.$request->string('q').'%'))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.subcategories.index', [
            'subcategories' => $subcategories,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.subcategories.create', [
            'subcategory' => new Subcategory([
                'is_active' => true,
                'category_id' => $request->integer('category') ?: null,
            ]),
            'categories' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(SubcategoryRequest $request): RedirectResponse
    {
        $subcategory = Subcategory::create($request->validated());

        return redirect()
            ->route('admin.subcategories.index')
            ->with('success', "Subcategory \"{$subcategory->name}\" created.");
    }

    public function show(Subcategory $subcategory): View
    {
        $subcategory->load('category');
        $products = $subcategory->products()->latest()->paginate(10);

        return view('admin.subcategories.show', compact('subcategory', 'products'));
    }

    public function edit(Subcategory $subcategory): View
    {
        return view('admin.subcategories.edit', [
            'subcategory' => $subcategory,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(SubcategoryRequest $request, Subcategory $subcategory): RedirectResponse
    {
        // Wrapped in a transaction because moving a subcategory to another
        // category also re-points its products (see Subcategory::booted()).
        DB::transaction(fn () => $subcategory->update($request->validated()));

        return redirect()
            ->route('admin.subcategories.index')
            ->with('success', "Subcategory \"{$subcategory->name}\" updated.");
    }

    public function destroy(Subcategory $subcategory): RedirectResponse
    {
        $name = $subcategory->name;

        DB::transaction(fn () => $subcategory->delete());

        return redirect()
            ->route('admin.subcategories.index')
            ->with('success', "Subcategory \"{$name}\" and all of its products were deleted.");
    }
}

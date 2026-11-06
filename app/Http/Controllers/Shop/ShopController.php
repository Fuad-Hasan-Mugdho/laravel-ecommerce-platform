<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\View\View;

class ShopController extends Controller
{
    /** Number of products previewed per subcategory on grouped pages. */
    private const PREVIEW_LIMIT = 8;

    /**
     * Home page: every active product, grouped under its subcategory and
     * category.
     */
    public function index(): View
    {
        $categories = Category::active()
            ->with([
                'subcategories' => fn ($q) => $q->active()
                    ->whereHas('products', fn ($p) => $p->active())
                    ->withCount(['products' => fn ($p) => $p->active()])
                    ->with(['products' => fn ($p) => $p->active()->latest()->limit(self::PREVIEW_LIMIT)]),
            ])
            ->whereHas('products', fn ($p) => $p->active())
            ->orderBy('name')
            ->get();

        return view('shop.index', compact('categories'));
    }

    public function category(string $slug): View
    {
        $category = Category::active()
            ->where('slug', $slug)
            ->with([
                'subcategories' => fn ($q) => $q->active()
                    ->whereHas('products', fn ($p) => $p->active())
                    ->withCount(['products' => fn ($p) => $p->active()])
                    ->with(['products' => fn ($p) => $p->active()->latest()->limit(self::PREVIEW_LIMIT)]),
            ])
            ->firstOrFail();

        return view('shop.category', compact('category'));
    }

    public function subcategory(string $slug): View
    {
        $subcategory = Subcategory::active()
            ->where('slug', $slug)
            ->whereHas('category', fn ($q) => $q->active())
            ->with('category')
            ->firstOrFail();

        $products = $subcategory->products()->active()->latest()->paginate(12);

        return view('shop.subcategory', compact('subcategory', 'products'));
    }

    public function product(string $slug): View
    {
        $product = Product::active()
            ->where('slug', $slug)
            ->with(['category', 'subcategory'])
            ->firstOrFail();

        $related = Product::active()
            ->where('subcategory_id', $product->subcategory_id)
            ->whereKeyNot($product->id)
            ->latest()
            ->take(4)
            ->get();

        return view('shop.product', compact('product', 'related'));
    }
}

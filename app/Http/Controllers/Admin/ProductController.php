<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(private readonly ProductImageService $images) {}

    public function index(Request $request): View
    {
        $products = Product::query()
            ->with(['category', 'subcategory'])
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->integer('category')))
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%'.$request->string('q').'%'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'product' => new Product(['is_active' => true]),
            'categories' => $this->categoriesWithSubcategories(),
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('image');
        $data['image'] = $this->images->store($request->file('image'));

        $product = Product::create($data);

        return redirect()
            ->route('admin.products.index')
            ->with('success', "Product \"{$product->name}\" created.");
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'subcategory']);

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', [
            'product' => $product,
            'categories' => $this->categoriesWithSubcategories(),
        ]);
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->safe()->except('image');

        if ($request->hasFile('image')) {
            $data['image'] = $this->images->replace($product, $request->file('image'));
        }

        $product->update($data);

        return redirect()
            ->route('admin.products.index')
            ->with('success', "Product \"{$product->name}\" updated.");
    }

    public function destroy(Product $product): RedirectResponse
    {
        $name = $product->name;

        $product->delete(); // image file removed by the model's "deleted" event

        return redirect()
            ->route('admin.products.index')
            ->with('success', "Product \"{$name}\" deleted.");
    }

    /**
     * Categories with their subcategories, used to pre-populate the
     * dependent subcategory dropdown (also refreshed via AJAX).
     */
    private function categoriesWithSubcategories()
    {
        return Category::with('subcategories:id,category_id,name')
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}

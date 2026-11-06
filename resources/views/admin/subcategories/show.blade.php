@extends('layouts.admin')
@section('title', $subcategory->name)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.subcategories.index') }}">Subcategories</a></li>
    <li class="breadcrumb-item active">{{ $subcategory->name }}</li>
@endsection
@section('actions')
    <a href="{{ route('admin.products.create') }}" class="btn btn-outline-primary"><i class="bi bi-plus-lg"></i> Add Product</a>
    <a href="{{ route('admin.subcategories.edit', $subcategory) }}" class="btn btn-primary"><i class="bi bi-pencil"></i> Edit</a>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5">Category</dt><dd class="col-7"><a href="{{ route('admin.categories.show', $subcategory->category) }}">{{ $subcategory->category->name }}</a></dd>
                    <dt class="col-5">Slug</dt><dd class="col-7"><code>{{ $subcategory->slug }}</code></dd>
                    <dt class="col-5">Status</dt><dd class="col-7"><x-status-badge :active="$subcategory->is_active" /></dd>
                    <dt class="col-5">Products</dt><dd class="col-7">{{ $products->total() }}</dd>
                    <dt class="col-5">Public URL</dt><dd class="col-7"><a href="{{ route('shop.subcategory', $subcategory->slug) }}" target="_blank">Open <i class="bi bi-box-arrow-up-right"></i></a></dd>
                </dl>
                @if ($subcategory->description)
                    <hr><p class="mb-0 text-muted">{{ $subcategory->description }}</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Products</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>Product</th><th>Price</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                    <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td class="d-flex align-items-center gap-2"><img src="{{ $product->image_url }}" class="thumb" alt=""> <a href="{{ route('admin.products.show', $product) }}" class="text-decoration-none fw-semibold">{{ $product->name }}</a></td>
                            <td><x-price :product="$product" /></td>
                            <td><x-status-badge :active="$product->is_active" /></td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <x-delete-button :action="route('admin.products.destroy', $product)" :confirm="'Delete product \''.$product->name.'\'?'" />
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">No products in this subcategory.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if ($products->hasPages())<div class="card-footer bg-white">{{ $products->links() }}</div>@endif
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin')
@section('title', 'Products')
@section('breadcrumb')<li class="breadcrumb-item active">Products</li>@endsection
@section('actions')
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Product</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <form method="GET" class="row g-2">
            <div class="col-sm-5 col-md-4">
                <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search products…">
            </div>
            <div class="col-sm-4 col-md-3">
                <select name="category" class="form-select">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-secondary">Filter</button>
                @if (request()->hasAny(['q', 'category']))<a href="{{ route('admin.products.index') }}" class="btn btn-link">Clear</a>@endif
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Product</th><th>Category / Subcategory</th><th>Price</th><th>Status</th><th>Added</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
            @forelse ($products as $product)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $product->image_url }}" class="thumb" alt="">
                            <div>
                                <a href="{{ route('admin.products.show', $product) }}" class="fw-semibold text-decoration-none">{{ $product->name }}</a>
                                <div class="small text-muted"><code>{{ $product->slug }}</code></div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $product->category->name }} <i class="bi bi-chevron-right small text-muted"></i> {{ $product->subcategory->name }}</td>
                    <td><x-price :product="$product" /></td>
                    <td><x-status-badge :active="$product->is_active" /></td>
                    <td class="small text-muted">{{ $product->created_at->format('d M Y') }}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('shop.product', $product->slug) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="View on store"><i class="bi bi-box-arrow-up-right"></i></a>
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <x-delete-button :action="route('admin.products.destroy', $product)" :confirm="'Delete product \''.$product->name.'\'?'" />
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-5">No products found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($products->hasPages())
        <div class="card-footer bg-white">{{ $products->links() }}</div>
    @endif
</div>
@endsection

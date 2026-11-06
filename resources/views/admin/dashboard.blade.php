@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    @foreach ([
        ['label' => 'Categories', 'value' => $stats['categories'], 'icon' => 'folder', 'route' => route('admin.categories.index')],
        ['label' => 'Subcategories', 'value' => $stats['subcategories'], 'icon' => 'folder2-open', 'route' => route('admin.subcategories.index')],
        ['label' => 'Products', 'value' => $stats['products'], 'icon' => 'box-seam', 'route' => route('admin.products.index')],
        ['label' => 'Active products', 'value' => $stats['activeProducts'], 'icon' => 'check2-circle', 'route' => route('admin.products.index')],
    ] as $card)
        <div class="col-6 col-xl-3">
            <a href="{{ $card['route'] }}" class="card text-decoration-none h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="fs-2 text-primary"><i class="bi bi-{{ $card['icon'] }}"></i></div>
                    <div>
                        <div class="text-muted small">{{ $card['label'] }}</div>
                        <div class="fs-3 fw-semibold text-dark">{{ $card['value'] }}</div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Latest products</span>
        <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg"></i> Add product</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Product</th><th>Category</th><th>Subcategory</th><th>Price</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse ($latestProducts as $product)
                <tr>
                    <td class="d-flex align-items-center gap-2"><img src="{{ $product->image_url }}" class="thumb" alt=""> {{ $product->name }}</td>
                    <td>{{ $product->category->name }}</td>
                    <td>{{ $product->subcategory->name }}</td>
                    <td><x-price :product="$product" /></td>
                    <td><x-status-badge :active="$product->is_active" /></td>
                    <td class="text-end"><a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No products yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

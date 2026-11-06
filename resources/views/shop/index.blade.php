@extends('layouts.shop')
@section('title', 'All Products')
@section('meta_description', 'Browse all products by category and subcategory at '.config('app.name').'.')

@section('content')
<div class="p-4 p-md-5 mb-4 rounded-3 bg-primary bg-gradient text-white">
    <h1 class="display-6 fw-semibold">Welcome to {{ config('app.name') }}</h1>
    <p class="lead mb-0">Browse our products, organised by category and subcategory.</p>
</div>

@forelse ($categories as $category)
    <div class="mb-5">
        <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-4">
            <h2 class="h4 mb-0"><i class="bi bi-folder2-open text-primary me-2"></i>{{ $category->name }}</h2>
            <a href="{{ route('shop.category', $category->slug) }}" class="small">Browse {{ $category->name }} <i class="bi bi-arrow-right"></i></a>
        </div>
        @foreach ($category->subcategories as $subcategory)
            @include('shop.partials.subcategory-section', ['subcategory' => $subcategory])
        @endforeach
    </div>
@empty
    <div class="text-center text-muted py-5">
        <i class="bi bi-box-seam display-4 d-block mb-3"></i>
        <p>No products are available yet. <a href="{{ route('admin.products.create') }}">Add a product</a> from the admin panel.</p>
    </div>
@endforelse
@endsection

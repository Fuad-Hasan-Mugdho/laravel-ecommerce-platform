@extends('layouts.shop')
@section('title', $product->name)
@section('meta_description', Str::limit($product->description, 155))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('shop.category', $product->category->slug) }}">{{ $product->category->name }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('shop.subcategory', $product->subcategory->slug) }}">{{ $product->subcategory->name }}</a></li>
    <li class="breadcrumb-item active">{{ $product->name }}</li>
@endsection

@section('content')
<div class="row g-4 mb-5">
    <div class="col-md-5">
        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="img-fluid rounded border w-100" style="aspect-ratio:1/1;object-fit:cover">
    </div>
    <div class="col-md-7">
        <h1 class="h2">{{ $product->name }}</h1>
        <p class="text-muted mb-3">
            <a href="{{ route('shop.category', $product->category->slug) }}" class="text-decoration-none">{{ $product->category->name }}</a>
            <i class="bi bi-chevron-right small"></i>
            <a href="{{ route('shop.subcategory', $product->subcategory->slug) }}" class="text-decoration-none">{{ $product->subcategory->name }}</a>
        </p>

        <div class="mb-4"><x-price :product="$product" size="fs-3" /></div>
        @if ($product->has_discount)
            <p class="text-success small"><i class="bi bi-tag"></i> You save ৳{{ number_format($product->old_price - $product->new_price, 2) }}</p>
        @endif

        <h5>Description</h5>
        <p class="text-secondary" style="white-space: pre-line">{{ $product->description }}</p>

        <button class="btn btn-primary btn-lg mt-3" disabled title="Coming soon"><i class="bi bi-cart-plus"></i> Add to cart</button>
    </div>
</div>

@if ($related->isNotEmpty())
    <h3 class="h5 section-title mb-3">More in {{ $product->subcategory->name }}</h3>
    <div class="row g-3">
        @foreach ($related as $item)
            @include('shop.partials.product-card', ['product' => $item->setRelation('subcategory', $product->subcategory)])
        @endforeach
    </div>
@endif
@endsection

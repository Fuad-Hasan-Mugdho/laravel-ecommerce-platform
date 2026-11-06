@extends('layouts.shop')
@section('title', $subcategory->name.' · '.$subcategory->category->name)
@section('meta_description', Str::limit($subcategory->description ?: 'Shop '.$subcategory->name.' at '.config('app.name'), 155))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('shop.category', $subcategory->category->slug) }}">{{ $subcategory->category->name }}</a></li>
    <li class="breadcrumb-item active">{{ $subcategory->name }}</li>
@endsection

@section('content')
<h1 class="h3 mb-1">{{ $subcategory->name }}</h1>
@if ($subcategory->description)<p class="text-muted">{{ $subcategory->description }}</p>@endif
<p class="small text-muted">{{ $products->total() }} product(s)</p>

<div class="row g-3">
    @forelse ($products as $product)
        @include('shop.partials.product-card', ['product' => $product->setRelation('subcategory', $subcategory)])
    @empty
        <p class="text-muted py-5 text-center">No products in this subcategory yet.</p>
    @endforelse
</div>

<div class="mt-4">{{ $products->links() }}</div>
@endsection
